# Test Plan

## Purpose

Build confidence that changes do not break important Sweetia customer and admin workflows. Customer storefront explanations have been supplied and the first Laravel feature-test slice is implemented. Admin features remain pending their explanations.

For each feature below, record:

- **Why it exists:** What need or problem led you to add it?
- **Intended behavior:** What should happen on success, invalid input, and relevant edge cases?

When the reason already makes the intended behavior clear, the Behavior field can remain brief.

## Repository test setup

- Laravel PHPUnit suites are configured in `phpunit.xml` for `tests/Unit` and `tests/Feature`.
- Existing tests cover authentication, profile updates, abandoned orders, selling reports, and pruning reports/notifications. There are also Laravel example tests.
- Factories exist for users, wrappers, products, orders, clients, states, and shippers.
- `phpunit.xml` now forces an in-memory SQLite database, so these tests cannot alter the development or seeded MySQL database.
- PHPUnit and Mockery are installed as development dependencies. Two MySQL-specific migrations use SQLite-compatible equivalents when running under the isolated test driver.
- The `StateFactory` includes the delivery and return costs required by the current schema.
- `package.json` currently has no Vue component-test or browser-test script. Vue tests and end-to-end browser tests would need test tooling configured.

## Recommended test layers

1. **Laravel feature tests first:** Routes, authorization, validation, database updates, imports, and Inertia page/prop responses. These should form the core suite.
2. **Vue component tests as needed:** Isolated reactive behavior that is hard to cover through an HTTP test, such as cart quantity controls or variant selection.
3. **Browser end-to-end tests for key journeys:** A small number of realistic flows across Laravel, Inertia, and Vue, such as browsing and placing an order. Use these for behavior that depends on actual navigation and browser interaction.
4. **Build and checks:** Keep the production Vite build and the relevant automated tests as the routine checks after changes.

## Safe test environment checklist

- [x] Configure a dedicated testing database (in-memory SQLite; never the normal development/seeded database).
- [x] Confirm test configuration runs migrations and resets test data safely.
- [x] Fake queued external delivery work in order tests; other external integration fakes remain to be added with their tests.
- [x] Record the backend test command: `php vendor/phpunit/phpunit/phpunit --configuration phpunit.xml`.
- [ ] Add and record Vue component/browser test commands and the production build check.

The storefront feature-test files pass when run directly with PHP 8.2. The legacy full suite still has unrelated auth/profile/report failures under SQLite; those are not counted as passing customer coverage.

## Feature inventory and specification checklist

For each item: add the explanation under **Why**, describe expected outcomes under **Behavior**, then check the status boxes as we work.

### Customer storefront

#### S-01 Home page

- **Scope:** Home page sections, product highlights, calls to action, and navigation.
- **Why:** just make sure it display all the content in the correct language,     
- **Behavior:** The page should render localized content and active wrapper highlights with their default product.
- **Status:** [x] Specified · [x] Tests added · [x] Passing
- **Coverage:** `StorefrontPagesTest` checks the Home Inertia component and translated active-wrapper/default-product props. Static Vue copy and cart-button interaction need browser/component coverage.

#### S-02 Shop catalog

- **Scope:** Product listing, search, sorting, pagination, empty results, and active-product visibility.
- **Why:**list all the available active wrappers names and main image, if the client clicks add to cart the wrapper default product must be added to the cart with quantity of 1 (if the product is not in the cart), if the product is already in the cart, the quantity should be increased by 1, test the search and sort options
- **Behavior:** _Please describe the intended behavior._
- **Status:** [x] Specified · [x] Tests added · [x] Passing
- **Coverage:** `StorefrontPagesTest` checks localized search, price sorting, and exclusion of inactive/empty wrappers. Add-to-cart cart-store quantity behavior and pagination interaction still need Vue/browser coverage.

#### S-03 Wrapper/product detail and variant selection

- **Scope:** Wrapper detail, available variants, default variant, quantity increments, and variant-specific options.
- **Why:** The wrapper detail page should display the wrapper details, available variants, the user can use the quick form to order the selected variant by selecting the variant and the quantity and press the place order button, as on the checkout page, the quick form has the same fields and validation as the checkout page. all the fields are optional except the phone, and when the 8 digit phone is typed, the quick form will automatically place an abandoned order (route name orders.abandoned),
- **Behavior:** The selected product must belong to the wrapper; otherwise the wrapper's default variant is selected. A valid phone-only quick order creates an abandoned order.
- **Status:** [x] Specified · [x] Tests added · [x] Passing
- **Coverage:** `StorefrontPagesTest` checks localized detail data, attached variant options, default selection, and 404 behavior. `AbandonedOrderTest` checks the phone-only abandoned-order endpoint. Quick-order browser interactions remain for later.

#### S-04 Cart

- **Scope:** Add/remove items, increase/decrease quantities, update increments, discounts, subtotals, shipping, and totals.
- **Why:** The cart is a shopping cart that allows users to add/remove items, increase/decrease quantities, update increments, discounts, subtotals, shipping, and totals.
- **Behavior:** _Please describe expected cart and pricing behavior, including limits._
- **Status:** [x] Why supplied · [ ] Tests added · [ ] Passing
- **Coverage gap:** Cart quantity increments (including each variant's 0.5/1 step), removal, discounts, subtotals, and client-side total updates are not covered by the backend slice yet; Vue/browser tooling is not configured.

#### S-05 Checkout and order placement

- **Scope:** Required customer information, delivery location, order totals, submission, and resulting order state.
- **Why:** The only required fiels is the phone number, it must be 8 digits (Tunisian phone number), it should not start with 0,1,6,8. It can start with 2,3,4,5,7,9. the other fields are optional and they have default values, when the user don't enter them, the default values should be used, when the user types an 8 digit phone an abondoned order should be placed (route name orders.abandoned), and a client places an order (route name orders.store), we should verify if he has an abandoned orders, if yes we should update the abandoned order to be pending, 
- **Behavior:** _Please describe success, validation errors, and failure behavior._
- **Status:** [x] Specified · [x] Tests added · [x] Passing
- **Coverage:** Checkout tests cover invalid phone boundaries (including the disallowed 8 prefix), optional customer fields, order submission, and abandoned-order reuse. Final order placement currently uses `FE.orders.place`.

#### S-06 Coupons and shipping calculations

- **Scope:** Coupon validation/discounts, free shipping rules, delivery fees, and total calculation.
- **Why:** Make sure that the used coupon code exists on the coupon_codes table and that its status is active, and that the total amount of the order is correct (some products have free shipping so it should not be added to the total amount. the total amount should be the sum of the products prices minus the discount amount plus the shipping fee (if any))
- **Behavior:** If a cart total is 100dt, and the user chose a state that its shipping fee is 8dt (the shipping fee is coming from the state), so the total amount should be 108dt. If the user chose a state that its shipping fee is 8dt , and the user chose a coupon code that gives a discount of 10%, so the total amount should be 98dt (100 - 10% discount + 8dt shipping). If The cart has a product that has a free shipping option, so the shipping fee should not be added to the total amount. so the total in this case will be (100 - 10% discount ) + 0dt shipping fee. (100 * 0.9) + 0 = 90
- **Status:** [x] Specified · [x] Tests added · [x] Passing
- **Coverage:** `CheckoutPricingTest` checks the three specified total examples, total tampering, active-only coupon lookup, rejection of inactive coupons during order placement, and rejection of client-tampered shipping fees. Pricing validation now takes shipping and coupon discount values from database records.

#### S-07 Languages and RTL layout

- **Scope:** English, French, Arabic translations; locale switching; Arabic direction and layout-sensitive controls.
- **Why:** Just make sure that the content is displayed in the correct language and direction and that all links and buttons are working as expected.
- **Status:** [x] Specified · [x] Tests added · [x] Passing
- **Coverage:** `StorefrontPagesTest` checks supported locale selection and locale session persistence. Arabic page direction and layout-sensitive controls require browser-level verification.

#### S-08 About, contact, terms, and privacy pages

- **Scope:** Page routes, translated content, contact links/address/map, and legal content rendering.
- **Why:** Just make sure that the content is displayed in the correct language and direction and that all links and buttons are working as expected.
- **Status:** [x] Specified · [x] Tests added · [x] Passing
- **Coverage:** `StorefrontPagesTest` checks that About, Contact, Terms, and Privacy routes render their expected Inertia components. Locale-specific body content, links, and map behavior need fuller assertions/browser verification.

### Admin and operations

#### A-01 Authentication and access control

- **Scope:** Login/logout, protected admin routes, account state/role restrictions, and profile/password changes.
- **Why:** _Please explain the account roles and access rules._
- **Behavior:** _Please describe who may access which areas and what blocked users should see._
- **Status:** [ ] Specified · [ ] Tests added · [ ] Passing

#### A-02 Products and product variants

- **Scope:** Product CRUD, translations, prices/discounts, status, and image/media management.
- **Why:** _Please explain how products and variants are managed._
- **Behavior:** _Please describe required fields, validation, visibility, and deletion behavior._
- **Status:** [ ] Specified · [ ] Tests added · [ ] Passing

#### A-03 Wrappers and variant ownership

- **Scope:** Wrapper CRUD, translations/media, variant configuration, default/free-shipping/quantity-step options, and one-wrapper-per-variant ownership.
- **Why:** _Please explain how wrappers and their variants should be managed._
- **Behavior:** _Please describe creation, editing, removal, default selection, and duplicate-ownership rules._
- **Status:** [ ] Specified · [ ] Tests added · [ ] Passing

#### A-04 Order management

- **Scope:** Order listing/details, status changes, editing order products, delivery/shipping data, and order history.
- **Why:** _Please explain the operational order workflow._
- **Behavior:** _Please describe allowed transitions, edits, and restrictions._
- **Status:** [ ] Specified · [ ] Tests added · [ ] Passing

#### A-05 Excel imports

- **Scope:** Import orders, shipping/status updates, pending updates, delivery dates, shipper updates, amount updates, and clients; validate templates, rows, and failures.
- **Why:** _Please explain why each import exists and who uses it._
- **Behavior:** _Please describe required columns, row-level failure behavior, duplicate handling, and partial/full success expectations._
- **Status:** [ ] Specified · [ ] Tests added · [ ] Passing

#### A-06 Clients

- **Scope:** Client CRUD, search, order relationships, and client import.
- **Why:** _Please explain how client records are used._
- **Behavior:** _Please describe uniqueness, validation, and import behavior._
- **Status:** [ ] Specified · [ ] Tests added · [ ] Passing

#### A-07 Shippers, locations, and delivery settings

- **Scope:** Shipper management, states/cities/localities, delivery fees and dates, and shipping settings.
- **Why:** _Please explain how delivery is configured and operated._
- **Behavior:** _Please describe location dependencies, fee rules, and delivery-date rules._
- **Status:** [ ] Specified · [ ] Tests added · [ ] Passing

#### A-08 Coupons

- **Scope:** Coupon CRUD, activation, validity dates, usage limits, discount type/value, and customer application.
- **Why:** _Please explain the business rules coupons support._
- **Behavior:** _Please describe expiration, eligibility, reuse, and stacking rules._
- **Status:** [ ] Specified · [ ] Tests added · [ ] Passing

#### A-09 Invoices

- **Scope:** Invoice creation/editing, order/client association, totals, and printable/downloadable output.
- **Why:** _Please explain when invoices are created and by whom._
- **Behavior:** _Please describe invoice numbering, amounts, and update/print behavior._
- **Status:** [ ] Specified · [ ] Tests added · [ ] Passing

#### A-10 Reports and dashboard statistics

- **Scope:** Selling/shipping reports, date/filter calculations, exports, dashboard metrics, and cleanup commands.
- **Why:** _Please explain what business decisions these reports support._
- **Behavior:** _Please describe date ranges, totals, filters, and export expectations._
- **Status:** [ ] Specified · [ ] Tests added · [ ] Passing

#### A-11 Notifications and notes

- **Scope:** Notification listing/read state and notes attached to relevant records.
- **Why:** _Please explain what events generate notifications and what notes are for._
- **Behavior:** _Please describe ownership, visibility, read state, and note lifecycle._
- **Status:** [ ] Specified · [ ] Tests added · [ ] Passing

### Integrations and reliability

#### I-01 Delivery and messaging integrations

- **Scope:** Queue jobs and calls to delivery providers, WhatsApp/SMS or other messaging services, and provider failures.
- **Why:** _Please explain which external services are essential._
- **Behavior:** _Please describe when calls happen, retry/failure expectations, and what users/admins see._
- **Status:** [ ] Specified · [ ] Tests added · [ ] Passing

#### I-02 Email, analytics, and other external events

- **Scope:** Email delivery, purchase analytics/tracking, and any other external events triggered by the application.
- **Why:** _Please explain which events should be sent and why._
- **Behavior:** _Please describe triggers, required data, consent rules, and failure behavior._
- **Status:** [ ] Specified · [ ] Tests added · [ ] Passing

## Suggested order of implementation

1. Make the test database safe and confirm the existing suite runs.
2. Add Laravel feature tests for wrapper ownership, product/wrapper management, cart/order pricing, checkout/order placement, and authorization.
3. Add tests for imports, coupons, shipping calculations, and reports.
4. Add Vue component tests only for complex client-side state that backend tests cannot verify.
5. Add a small browser suite for browse → choose variant → cart → checkout, plus one Arabic/RTL smoke journey and one admin workflow.
6. Add the checks to CI so the same suite runs consistently before changes are merged or released.
