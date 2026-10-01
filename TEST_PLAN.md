# Test Plan

## Purpose

Build confidence that changes do not break important Sweetia customer and admin workflows. Customer storefront, A-01, and A-02 test cases are implemented; remaining admin features will be added one at a time.

For each feature below, record:

- **Why it exists:** What need or problem led you to add it?
- **Intended behavior:** What should happen on success, invalid input, and relevant edge cases?

When the reason already makes the intended behavior clear, the Behavior field can remain brief.

## Repository test setup

- `composer test` runs the planned Laravel feature tests in `tests/Feature/FrontEnd` and `tests/Feature/Admin`. Existing tests that did not match the application schema or current routes were removed at the user's request.
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
- [x] Record the backend test commands: `composer test` runs the planned suite; `composer test Feature\\Admin\\ProductManagementTest.php` runs one file (path relative to `tests`).
- [ ] Add and record Vue component/browser test commands and the production build check.

The planned suite currently passes with PHP 8.2 using in-memory SQLite. PHPUnit reports one deprecation notice that remains to be investigated.

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
- **Why:** Authentication is a fundamental part of any web application and should be tested thoroughly
- **Behavior (inferred from current middleware/routes):** Guests are redirected to login for protected pages; moderators can access regular admin pages but receive 403 on admin-only employee management; admins can access those admin-only pages; banned users are redirected to the banned page, while active users are redirected away from it. Authenticated users can update their profile and password; valid login/logout changes their session as expected.
- **Status:** [x] Specified · [x] Tests added · [x] Passing
- **Coverage:** `AuthenticationAccessTest` covers guest protection, login/logout, invalid credentials, moderator/admin access, banned-user routing, profile updates, and password changes. This policy is inferred from the current code and awaits your confirmation.

#### A-02 Products and product variants

- **Scope:** Product CRUD, translations, prices/discounts.
- **Why:** We must be able to add, edit, delete and list products(variants), the products are created in order to be assigned to wrappers and then. is important to know that the products table is what i'm calling variants, so a product is a variant, and the wrapper can contain variants. (products)
- **Behavior (inferred from current requests/controller and UI):** An admin can create, update, list, search, and delete variants. Creation and update require a positive price, shipping name, and a name in each of the English, French, and Arabic translations. Discount and discount type are optional; when supplied, the type is `percentage` or `fixed`. Product prices entered in dinars are stored in millimes. Moderators may view product pages, but the interface hides creation, edit, and delete controls, and create/update submissions are rejected. Products with no order history are permanently deleted; products with order history are soft-deleted.
- **Status:** [x] Specified · [x] Tests added · [x] Passing
- **Coverage:** `ProductManagementTest` covers creation/translations, required-field validation, update/conversion, moderator page access and request restrictions, locale-specific list search, and permanent deletion without order history. Vue visibility for sidebar/list/detail actions and the soft-delete branch for products with historical orders are not covered by backend tests yet. The policy is inferred from current code and awaits your confirmation.

#### A-03 Wrappers and variant ownership

- **Scope:** Wrapper CRUD, translations/media, variant configuration, default/free-shipping/quantity-step options, and one-wrapper-per-variant ownership.
- **Why:** The idea from the wrapper is to be a container for variants of the same products, so for example if we have a variant called "2kg of Almond Baklawa (price 70dt)" and a variant called "1kg of almond Baklawa (39dt)", so the main product is called "Almond Baklawa" and it should contains the 2 variants, so in this case we call the wrapper "Almond Baklawa", and the variants are "2kg of Almond Baklawa (price 70dt)" and "1kg of almond Baklawa (39dt)". the wrapper is what the user will see on the shop page, so it should have an image, and the price will be the price of the default variant.
- **Behavior (inferred from current requests/controller and UI):** Admins can create and edit wrappers with English, French, and Arabic titles, optional descriptions, active state, images, and at least one variant. Exactly one variant must be default; the wrapper price is that variant's price. Variant options include display order, free shipping, and quantity step 0.5 or 1. A variant can belong to only one wrapper. The edit form offers variants already in that wrapper and unassigned variants, but excludes variants owned elsewhere. Moderators can view the wrapper list/details, but the existing `isAdminMiddleware` blocks create/edit pages and all mutation routes. The interface also hides create, edit, and delete controls. Deleting a wrapper detaches its variants without deleting the products.
- **Status:** [x] Specified · [x] Tests added · [x] Passing
- **Coverage:** `WrapperManagementTest` covers create/translations/image, zero or multiple defaults, default-variant price, cross-wrapper ownership on create/update, edit-form variant availability, pivot settings, moderator access to read pages versus denied mutation pages/routes, and deleting a wrapper while preserving variants. Sidebar and button visibility are expressed in Vue conditions but are not browser-tested. The behavior is inferred from current code and awaits your confirmation.

#### A-04 Order management

- **Scope:** Order listing/details, status changes, editing order products, delivery/shipping data, and order history.
- **Why:** We need to be able to see the orders, filter them, edit them (client name, state_id, city_id, locality_id, delivery_date, shipper_id, phone, address, phone2, free_shipping), also we can update the order products (cart), also we can update the order status using a process, the order first can be abandoned (the user typed his phone number but he didn't place the order, we captured his phone and the current cart) or pending (the user actually placed the order or the admin created the order manually and made it pending), then the pending order before changing its status the admin must make a confirmation call, after that confirmation call there are 3 scenarios that can happen : 
- **Scenario 1: Confirmation and Processing**
  - **Admin Action:** The admin updates the order info based on its talk with the client (client name, state_id, city_id, locality_id, delivery_date, shipper_id, phone, address, phone2, free_shipping) and updates the products and products quantities as the client wants and then clicks the "confirm" button in the frontend.
  - **Result:** The order status changes to "confirmed", if the shipper has an api key we make a request to the shipping company with the order info (we can avoid this step on the test)
  - **Behavior:**
    - The order is now locked for editing (we can update only abandoned or pending products, the other orders can be updated via actions, we will talk about that later).
    - The order status is updated to "Confirmed".

- **Scenario 2: User Cancellation**
  - **Admin Action:** Admin clicks the "Cancel" button in the frontend.
  - **Result:** The order status changes to "cancelled"
  - **Behavior:**
    - The order is now locked for editing (we can update only abandoned or pending products, the other orders can be updated via actions, we will talk about that later).
    - The order status is updated to "Cancelled".

- **Scenario 3: The Order Marked as NRP (Ne Repond pas)**
  - **Admin Action:** if the clients doesn't respond after 3 calls, the admin clicks the "Mark as NRP" button in the frontend.
  - **Result:** The order status stays as abandoned or pending but it will be attached to the nrp table, the order marked as nrp can't be listed in the main orders list (There is another view responsible for nrp orders), for each time the admin calls the client and the client doesn't respond, the admin will click the "NRP" button to increment the number of tries, until the client respond and confirm or cancel the order or the admin decide to cancel it, if the admin decide to cancel it the process will be the same as scenario 2. if the nrp order is canceled or confirmed, it will automatically removed from the nrp list.
  - **Behavior:**
    - The nrp order is now locked for editing (we can update only abandoned or pending products, the other orders can be updated via actions, we will talk about that later).

- **Behavior:** Pending/abandoned orders can be marked NRP; each later NRP action increments the try count. NRP orders are excluded from the main orders list and appear in the NRP list. Confirming or canceling an NRP order removes its NRP record. Confirmed, canceled, and NRP orders are locked for direct editing; only pending/abandoned orders allow product edits.
- **Status:** [x] Specified · [x] Tests added · [x] Passing

- **Coverage:** `OrderManagementTest` covers repeated NRP marking, main-list exclusion and NRP-list visibility, confirmation/cancellation clearing the NRP record, product replacement and amount calculation, and order-information updates syncing the client record and free-shipping amount. Enforcement of the edit-lock policy still needs a dedicated assertion.

#### A-05 Excel imports

- **Scope:** Import orders, shipping/status updates, pending updates, delivery dates, shipper updates, amount updates, and clients; validate templates, rows, and failures.
- **Why:** On the Actions.vue component the admin can see 6 actions can make via importing excel files. we do that because it's faster to update a large number of orders at once using excel files instead of updating them one by one. and also to update reach a goal we can no longer able to reach it without these actions. for example confirmed order cannot be updated, but what if we confirmed an order and then we noticed that we did a mistake in the order like the client didn't want the product anymore, so we can't update the order, we can cancel the order or delete it and create a new one, but this is not a good behaviour, the best is to return the order to pending status and update it and confirm it. so the user in this case must provides an excel file that contains one column called "id" which is the order id, then click submit and the file will be dispatched to a job that will loop through the orders and update their statuses to pending.
- the import orders action : 
-- the admin can import orders from an excel file, the excel file must contain the following columns : client_name, phone, state_id, address, products, all the fields are optional and have a default value in case they are not provided except the phone field, for the products column, its a string contains the name of products, this string cannot be matched with the current products on the db, so the order will be created with a default product, this product is defined via "DEFAULT_PRODUCT_ID" variable defined in the .env file, and the products column string will be added as a note on the order. so the admin who will call the client knows what's the products the client wants. and if the clients confirmed the order the admin updates the default product by the real products.
- the status update action : 
-- an action that will update the status of orders to delivered or returned only, and only confirmed or shipped orders can be updated using this action. the required columns are id (of the order) and "statut" (status but in french)
- the pending updates action : we already talked about this
- the delivery dates action : 
-- an action that will update the delivery date of orders only, the required fields are id (of the order), and after the import the admin chooses the delivery_date via a datepicker on the view and then presses the submit button so the job will loop through the orders and update their delivery dates.
- the shipper updates action : 
-- an action that will update the shipper of orders only, the required fields are id (of the order) and "livreur" (shipper but in french), for the "livreur" column it will be the shipper id
- the amount updates action : 
-- an action that will update the amount of orders, we use this because some clients pay some part of the order amount and the rest when the delivery, so we need to remove the paid part from the total amount and the rest will be the amount to pay on delivery, so we need to update the total amount of the order. the required fields are the id (of the order), and "montant" (amount but in french), this amount must be in millimes (1000 millimes = 1 dinar). 
- the clients action : 
-- an action that will import clients from an excel file, the excel file must contain the following columns : nom, mobile, mobile2, gouvernorat(the id of the state), adresse, all the fields are required except mobile2, if a client exist with the same phone number the row will be escaped to avoid duplication. (currently i'm comparing only the phone number, i want also to check the phone2, maybe the mobile2 provided on the import is the same as the phone, that's why we need to check both, so update the ClientImport.php to take care of that)
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
