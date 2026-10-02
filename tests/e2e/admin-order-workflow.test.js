import assert from "node:assert/strict";
import { spawnSync } from "node:child_process";
import { test } from "node:test";
import puppeteer from "puppeteer";

const baseUrl = (process.env.E2E_BASE_URL || "http://ecommerce.test").replace(/\/$/, "");
const phpBinary = process.env.PHP_BINARY || (process.platform === "win32" ? "C:\\PHP\\php.exe" : "php");

test("admin can add items, change quantity, and place a pending NRP order with free shipping", { timeout: 120_000 }, async () => {
    const browser = await puppeteer.launch({
        headless: process.env.E2E_HEADLESS === "true",
        slowMo: process.env.E2E_HEADLESS === "true" ? 0 : 120,
        args: ["--no-sandbox", "--disable-gpu", "--disable-dev-shm-usage", "--no-proxy-server"],
    });
    const page = await browser.newPage();
    await page.setViewport({ width: 1440, height: 1000 });

    const marker = `E2E Admin Order ${Date.now()}-${Math.random().toString(36).slice(2, 8)}`;
    const phone = `2${String(Date.now()).slice(-7)}`;
    let testOrderMayExist = false;

    try {
        await page.goto(`${baseUrl}/login`, { waitUntil: "domcontentloaded" });
        console.log("Opened login page.");
        await page.type('input[type="email"]', process.env.E2E_ADMIN_EMAIL || "admin@gmail.com");
        await page.type('input[type="password"]', process.env.E2E_ADMIN_PASSWORD || "password");
        await page.click('input[type="submit"]');
        console.log("Submitted seeded admin login.");
        await page.waitForFunction(() => window.location.pathname === "/dashboard");
        console.log("Logged in and reached the dashboard.");

        await page.evaluate(() => sessionStorage.removeItem("cart"));
        await page.goto(`${baseUrl}/orders/create`, { waitUntil: "domcontentloaded" });
        console.log("Opened admin order creation page.");

        const addToCartButton = await findButton(page, "add to cart");
        assert.ok(addToCartButton, "the order form should show products with an add-to-cart action");
        await addToCartButton.click();
        console.log("Added the first available product to the admin cart.");

        await page.waitForFunction(() => {
            const cart = JSON.parse(sessionStorage.getItem("cart") || "[]");
            return cart.length === 1;
        });
        const quantityStep = await page.evaluate(() => {
            const [product] = JSON.parse(sessionStorage.getItem("cart") || "[]");
            return Number(product.update_quantity_by ?? product.update_quantity ?? 1);
        });
        const addQuantityButton = await findButton(page, "+");
        assert.ok(addQuantityButton, "the cart should show a quantity increase control");
        await addQuantityButton.click();
        await addQuantityButton.click();

        const expectedQuantity = 1 + 2 * quantityStep;
        await page.waitForFunction(
            (expected) => Number(document.querySelector("input[readonly]")?.value) === expected,
            {},
            expectedQuantity,
        );

        const existingClientResponse = await page.evaluate(async (value) => {
            const response = await fetch(`/clients/${value}/search`, { headers: { Accept: "application/json" } });
            return response.json();
        }, phone);
        assert.deepEqual(existingClientResponse, [], "the generated test phone should not belong to an existing client");

        await fill(page, "#phone", phone);
        await fill(page, "#full-name", marker);
        await fill(page, "#address", "Automated browser test address");
        await selectFirstOption(page, "#state_id");
        console.log("Filled the order's customer and delivery details.");
        await waitForSelectOptions(page, "#city_id");
        await selectFirstOption(page, "#city_id");
        await waitForSelectOptions(page, "#locality_id");
        await selectFirstOption(page, "#locality_id");
        await selectFirstOption(page, "#shipper_id");
        await page.$eval(".datepicker", (input) => {
            const date = new Date();
            date.setDate(date.getDate() + 2);
            input._flatpickr.setDate(date, true);
        });
        await page.select("#status-input", "pending");
        await page.click("#nrp-field");
        await page.click("#freeShipping-field");

        testOrderMayExist = true;
        await page.click('form input[type="submit"]');
        await page.waitForFunction(() => {
            const cart = JSON.parse(sessionStorage.getItem("cart") || "[]");
            return cart.length === 0;
        });

        await page.goto(`${baseUrl}/nrp?phone=${encodeURIComponent(phone)}`, { waitUntil: "domcontentloaded" });
        await page.waitForFunction((value) => document.body.innerText.includes(value), {}, phone);
        const nrpPage = await page.$eval("#app", (root) => JSON.parse(root.getAttribute("data-page")));
        const nrpEntry = nrpPage.props.nrps.data.find((entry) => entry.phone === phone);
        assert.ok(nrpEntry, "the pending order marked as NRP should appear in the NRP list");
        const orderHref = await page.$eval(`a[href*="/orders/${nrpEntry.order_id}"]`, (link) => link.getAttribute("href"));
        assert.ok(orderHref, "the NRP result should link to the order details page");

        await page.goto(new URL(orderHref, baseUrl).toString(), { waitUntil: "domcontentloaded" });
        const inertiaPage = await page.$eval("#app", (root) => JSON.parse(root.getAttribute("data-page")));
        const order = inertiaPage.props.order.data;
        assert.equal(order.status, "pending");
        assert.equal(order.free_shipping, true);
        assert.equal(Number(order.shipping_cost), 0);
        assert.equal(Number(order.products[0].pivot.quantity), expectedQuantity);
        assert.equal(Number(order.amount), Number((Number(order.products[0].pivot.price) * expectedQuantity).toFixed(3)));
        assert.equal(Number(order.nrp.tries), 1);
        assert.equal(order.client_name, marker);
        assert.equal(order.phone, phone);

        console.log(`Verified admin order #${order.id}: quantity ${expectedQuantity}, pending + NRP, free shipping.`);
    } finally {
        if (testOrderMayExist) {
            const cleanup = spawnSync(phpBinary, ["tests/e2e/support/cleanup-admin-order.php"], {
                cwd: process.cwd(),
                env: { ...process.env, E2E_ORDER_PHONE: phone, E2E_ORDER_MARKER: marker },
                encoding: "utf8",
            });
            if (cleanup.status !== 0) {
                console.error(cleanup.stdout, cleanup.stderr);
                await browser.close();
                assert.fail("Could not remove the browser test order and its test client from the seeded database.");
            }
        }
        await browser.close();
    }
});

async function findButton(page, expectedText) {
    const buttons = await page.$$("button");
    for (const button of buttons) {
        const text = await button.evaluate((element) => element.innerText.trim().toLowerCase());
        if (text === expectedText.toLowerCase()) return button;
    }
    return null;
}

async function fill(page, selector, value) {
    const input = await page.waitForSelector(selector);
    await input.click({ clickCount: 3 });
    await input.press("Backspace");
    await input.type(value);
}

async function waitForSelectOptions(page, selector) {
    await page.waitForFunction(
        (selectSelector) => document.querySelector(selectSelector)?.options.length > 1,
        {},
        selector,
    );
}

async function selectFirstOption(page, selector) {
    await waitForSelectOptions(page, selector);
    await page.$eval(selector, (select) => {
        select.selectedIndex = 1;
        select.dispatchEvent(new Event("change", { bubbles: true }));
    });
}
