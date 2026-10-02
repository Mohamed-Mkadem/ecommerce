import assert from "node:assert/strict";
import { spawnSync } from "node:child_process";
import { test } from "node:test";
import puppeteer from "puppeteer";

const baseUrl = (process.env.E2E_BASE_URL || "http://ecommerce.test").replace(/\/$/, "");
const phpBinary = process.env.PHP_BINARY || (process.platform === "win32" ? "C:\\PHP\\php.exe" : "php");
const seededWrapperSlug = process.env.E2E_STOREFRONT_WRAPPER_SLUG || "necessitatibus-dolorem-tempore-placeat-ad";

test("customer can switch to Arabic, search a pastry, update the cart, apply BG10, and place an order", { timeout: 150_000 }, async () => {
    const browser = await puppeteer.launch({
        headless: process.env.E2E_HEADLESS === "true",
        slowMo: process.env.E2E_HEADLESS === "true" ? 0 : 120,
        args: ["--no-sandbox", "--disable-gpu", "--disable-dev-shm-usage", "--no-proxy-server"],
    });
    const page = await browser.newPage();
    await page.setViewport({ width: 1440, height: 1000 });

    const marker = `E2E Storefront Order ${Date.now()}-${Math.random().toString(36).slice(2, 8)}`;
    const phone = `2${String(Date.now()).slice(-7)}`;
    let orderMayExist = false;

    try {
        await page.goto(`${baseUrl}/shop`, { waitUntil: "domcontentloaded" });
        await page.waitForSelector("#app[data-page]");
        assert.match(await languageButtonText(page), /English/i, "the journey should begin in English");
        console.log("Opened the shop in English.");

        const wrapperPage = await readInertiaPage(page);
        const wrappers = wrapperPage.props.wrappers.data;
        const wrapper = wrappers.find((item) => item.slug === seededWrapperSlug);
        assert.ok(wrapper, `seeded wrapper ${seededWrapperSlug} should be listed in the shop`);
        assert.ok(wrapper.default_product?.translations?.some((translation) => translation.locale === "ar"));

        await openLanguageDropdown(page);
        const localeSwitchResponse = page.waitForResponse((response) =>
            response.request().url().includes("/lang/ar"),
        );
        await clickText(page, "li", "العربية");
        await localeSwitchResponse;
        await page.waitForFunction(() => document.documentElement.getAttribute("dir") === "rtl");
        await page.waitForFunction(() => document.body.innerText.includes("العربية"));
        console.log("Switched the storefront to Arabic using the language switcher.");

        // Read localized fixture data without navigating away from the shop page.
        // The subsequent search and product selection are performed through the visible UI.
        const detailPage = await fetchInertiaPage(page, `/shop/${seededWrapperSlug}`);
        const localizedTitle = detailPage.props.wrapper.title;
        const variants = detailPage.props.variants;
        assert.ok(localizedTitle, "the seeded wrapper should have an Arabic title");
        assert.ok(variants.length > 1, "the seeded wrapper should have multiple variants");
        const selectedVariant = variants.find((variant) => !variant.is_default) || variants[1];
        const quantityStep = Number(selectedVariant.update_quantity || 1);
        const expectedQuantity = Number((1 + quantityStep).toFixed(3));

        await fill(page, "#search", localizedTitle);
        await page.waitForFunction((slug) => {
            const result = [...document.querySelectorAll('a[href]')]
                .find((link) => link.getAttribute("href")?.endsWith(`/shop/${slug}`));
            return window.location.search.includes("search=") && result;
        }, { timeout: 15_000 }, seededWrapperSlug);
        console.log(`Searched the Arabic shop for “${localizedTitle}”.`);

        await clickLinkToWrapper(page, seededWrapperSlug);
        await page.waitForFunction((slug) => window.location.pathname === `/shop/${slug}`, {}, seededWrapperSlug);
        const displayedWrapper = await fetchInertiaPage(page, `/shop/${seededWrapperSlug}`);
        assert.equal(displayedWrapper.props.wrapper.title, localizedTitle);
        const variantToChoose = displayedWrapper.props.variants.find((item) => item.id === selectedVariant.id);
        assert.ok(variantToChoose, "the selected variant should be available on the detail page");

        await clickVariant(page, variantToChoose.name);
        await page.waitForFunction((name) => {
            const selected = document.querySelector('button[aria-pressed="true"]');
            return selected?.innerText.includes(name);
        }, {}, variantToChoose.name);
        console.log(`Opened the pastry and selected variant “${variantToChoose.name}”.`);

        await clickButtonContaining(page, "أضف إلى السلّة");
        await page.waitForFunction((id) => {
            const cart = JSON.parse(sessionStorage.getItem("cart") || "[]");
            return cart.some((item) => Number(item.id) === Number(id));
        }, {}, selectedVariant.id);
        console.log("Added the selected variant to the cart.");

        await page.goto(`${baseUrl}/cart`, { waitUntil: "domcontentloaded" });
        await page.waitForFunction((id) => {
            const cart = JSON.parse(sessionStorage.getItem("cart") || "[]");
            return cart.some((item) => Number(item.id) === Number(id));
        }, {}, selectedVariant.id);
        const increaseButton = await findButtonByAriaSuffix(page, "+");
        assert.ok(increaseButton, "cart should display a quantity increase control");
        await increaseButton.click();
        await page.waitForFunction(({ id, expected }) => {
            const item = JSON.parse(sessionStorage.getItem("cart") || "[]").find((product) => Number(product.id) === Number(id));
            return item && Number(item.quantity) === expected;
        }, {}, { id: selectedVariant.id, expected: expectedQuantity });
        console.log(`Updated cart quantity to ${expectedQuantity}.`);

        await page.goto(`${baseUrl}/checkout`, { waitUntil: "domcontentloaded" });
        await page.waitForSelector("#full-name");
        await fill(page, "#code", "BG10");
        await clickButtonContaining(page, "تطبيق");
        await page.waitForFunction(() => document.body.innerText.includes("BG10") && document.querySelector("#code")?.value === "");
        console.log("Applied the active BG10 coupon.");

        await fill(page, "#full-name", marker);
        await fill(page, "#phone", phone);
        await fill(page, "#address", "Automated storefront browser test address");

        orderMayExist = true;
        await clickCheckoutSubmit(page);
        await page.waitForFunction(
            () => document.querySelector("#app")?.innerText.includes("تم إنشاء طلبك بنجاح"),
            { timeout: 30_000 },
        );

        const verify = spawnSync(phpBinary, ["tests/e2e/support/verify-storefront-order.php"], {
            cwd: process.cwd(),
            env: { ...process.env, E2E_ORDER_PHONE: phone, E2E_ORDER_MARKER: marker, E2E_EXPECTED_PRODUCT_ID: String(selectedVariant.id), E2E_EXPECTED_QUANTITY: String(expectedQuantity) },
            encoding: "utf8",
        });
        if (verify.status !== 0) {
            console.error(verify.stdout, verify.stderr);
            assert.fail("The storefront order did not match the expected coupon, product, and quantity.");
        }
        const order = JSON.parse(verify.stdout);
        assert.equal(order.status, "pending");
        assert.equal(order.source, "client");
        assert.equal(order.coupon_code, "BG10");
        assert.equal(Number(order.product_id), Number(selectedVariant.id));
        assert.equal(Number(order.quantity), expectedQuantity);
        assert.equal(order.client_name, marker);
        assert.equal(order.phone, phone);
        console.log(`Verified storefront order #${order.id} with BG10 and quantity ${expectedQuantity}.`);
    } finally {
        if (orderMayExist) {
            const cleanup = spawnSync(phpBinary, ["tests/e2e/support/cleanup-storefront-order.php"], {
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

async function readInertiaPage(page) {
    return page.$eval("#app", (root) => JSON.parse(root.getAttribute("data-page")));
}

async function fetchInertiaPage(page, path) {
    return page.evaluate(async (urlPath) => {
        const response = await fetch(urlPath, { credentials: "same-origin" });
        const html = await response.text();
        const document = new DOMParser().parseFromString(html, "text/html");
        const root = document.querySelector("#app[data-page]");
        if (!root) throw new Error(`Unable to read page data for ${urlPath}`);
        return JSON.parse(root.getAttribute("data-page"));
    }, path);
}

async function languageButtonText(page) {
    const buttons = await page.$$("button");
    for (const button of buttons) {
        const text = (await button.evaluate((element) => element.innerText.trim())).replace(/\s+/g, " ");
        if (["English", "Français", "العربية"].some((label) => text.includes(label))) return text;
    }
    return "";
}

async function openLanguageDropdown(page) {
    const buttons = await page.$$("button");
    for (const button of buttons) {
        if ((await button.evaluate((element) => element.innerText.trim())).includes("English")) {
            await button.click();
            return;
        }
    }
    assert.fail("Could not find the English language switcher control.");
}

async function clickText(page, selector, text) {
    const elements = await page.$$(selector);
    for (const element of elements) {
        if ((await element.evaluate((node) => node.innerText.trim())).includes(text)) {
            await element.click();
            return;
        }
    }
    assert.fail(`Could not find ${selector} containing ${text}.`);
}

async function fill(page, selector, value) {
    const input = await page.waitForSelector(selector);
    await input.click({ clickCount: 3 });
    await input.press("Backspace");
    await input.type(value);
}

async function clickLinkToWrapper(page, slug) {
    const links = await page.$$("a[href]");
    for (const link of links) {
        const href = await link.evaluate((element) => element.getAttribute("href"));
        if (href?.endsWith(`/shop/${slug}`)) {
            await link.click();
            return;
        }
    }
    assert.fail(`Could not find the shop result link for ${slug}.`);
}

async function clickVariant(page, variantName) {
    const buttons = await page.$$("button[aria-pressed]");
    for (const button of buttons) {
        if ((await button.evaluate((element) => element.innerText)).includes(variantName)) {
            await button.click();
            return;
        }
    }
    assert.fail(`Could not find variant option “${variantName}”.`);
}

async function findButtonByAriaSuffix(page, suffix) {
    const buttons = await page.$$("button[aria-label]");
    for (const button of buttons) {
        const label = await button.evaluate((element) => element.getAttribute("aria-label"));
        if (label?.endsWith(suffix)) return button;
    }
    return null;
}

async function clickButtonContaining(page, text) {
    const buttons = await page.$$("button");
    for (const button of buttons) {
        if ((await button.evaluate((element) => element.innerText.trim())).includes(text)) {
            await button.click();
            return;
        }
    }
    assert.fail(`Could not find a button containing “${text}”.`);
}

async function clickCheckoutSubmit(page) {
    const submit = await page.$("form input[type='submit']");
    if (submit) {
        await submit.click();
        return;
    }
    assert.fail("Could not find the checkout place-order submit control.");
}
