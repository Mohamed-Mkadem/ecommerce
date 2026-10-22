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

- **Coverage:** `OrderManagementTest` covers repeated NRP marking, main-list exclusion and NRP-list visibility, confirmation/cancellation clearing the NRP record, product replacement and amount calculation, order-information updates syncing the client record and free-shipping amount, and manual admin order creation with paid/free shipping, total validation and NRP attachment only for pending orders. Enforcement of the edit-lock policy still needs a dedicated assertion.

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
- **Behavior:** Order imports require a valid phone and use the configured default product while preserving the requested products as an order note. Status imports use `id` and `statut`, accept `delivered`, `returned`, `canceled`, or `shipped`, and allow updates only for confirmed or shipped orders. Pending imports use `id` to return orders to pending. Delivery-date imports use `id` plus a selected date; shipper imports use `id` and `livreur`; amount imports use `id` and `montant` in dinars, converted to millimes when stored. Client imports require `nom`, `mobile`, `adresse`, and `gouvernorat`; `mobile2` is optional. Skip client rows if either supplied number already matches either phone field on an existing client.
- **Status:** [x] Specified · [x] Tests added · [x] Passing
- **Coverage:** `ExcelImportTest` covers all six order import actions and client import, including default-product order creation/notes, all four allowed status values and status eligibility, multiple-row date updates, shipper assignment, DT-to-millime amount conversion, client secondary-phone storage, and duplicate detection across both phone fields. Detailed validation feedback for malformed files and all row-level failure combinations remains uncovered.

#### A-06 Clients

- **Scope:** Client CRUD, search, order relationships, and client import.
- **Why:** the admin can add clients manually or import them from an excel file. (we are alrerady tested the clients import on the previous tests), this feature allow the admin to add clients to the system from external db, the admin can list all the clients, filter them based on the filters available on the form, he show a specific client on the details page, the details page must show the client info, and all the orders made by this client, and the admin notes on this specific client. the admin can also update the client info, (the phone, phone2, state_id, city_id, locality_id, address and name ) when creating or updating the client manually all the fields are required except phone2, and the phone must be unique for all the clients.
- **Behavior:** Admins can manually create and update clients with name, phone, address, state, city, and locality; phone2 is optional. Phone numbers must be valid, and the primary phone must be unique. The client list supports name, phone (including phone2), and state filters. Client details show the client information, associated orders, and client notes. Client import is covered under A-05.
- **Status:** [x] Specified · [x] Tests added · [x] Passing
- **Coverage:** `ClientManagementTest` covers create with and without phone2, required-field/phone validation, update while retaining the same primary phone, name/phone/state filters, and client detail rendering with an order and note. Other list filters and deletion behavior are not covered.

#### A-07 Shippers, locations, and delivery settings

- **Scope:** Shipper management, states/cities/localities, delivery fees and dates, and shipping settings.
- **Why:** as you know each order must be delivered to the client via a shipper (shippers table), the shipper can be a shipping company that provides an api so once we click confirm the order info will be sent to the shipper's api and the order will be assigned to the shipper and a tracking number will be generated and sent back to the order. (currently on the OrderController you can see that when we update the status to confirmed we check if the name of the shipper is "Navex", if yes we pass the order to a job called SendOrderToNavex, this job will do the job and send the order to Navex's api, currently we will not do that, because the used apis are for the previous project, that part of code will be different from client to client, so we will implement that later ). also the shipper can be an individual shipper, these shipper orders will be handled by the business staff itself, they will generate for it a shipping report that contains all the confirmed orders for that specific date, and give it to the shipper with the orders, so the shipper will deliver the orders to the clients.
- states/cities/localities and delivery fees : i mean by states the 24 Tunisian "gouvernorats", the cities are the "delegations", and the localities are the "imadates", currently i'm seeding them from StateSeeder.php file (created manually based on Tunisian Post data), The delivery fee is based on the state not the city or the locality, so for the 24 states we have 24 differnt/same delivery fees. the admin is free to set the shipping cost for each state as he want and as he negotiates with the shipper. the delivery fees are stored on the shipping_cost column on the states table. also each state has a return_cost that is stored on the return_cost column on the states table. the return_cost is the cost the business owner will pay for each returned order (the client didn't accept the order for any reason), also you'll see a column called delivery_cost this is the amount the admin will actually pay for the shipper, so for example Tunis has a shipping_cost of 8dt (the amount the client will pay for the shipping), and the delivery_cost is 7dt (the amount the admin will pay for the shipper, and the admin will preserve 1dt for himself), so the admin makes 1dt profit on this order, the return cost is 5dt, so if the client returns the order the business owner will pay 5dt to the shipper. (the delivery_cost and return cost doesn't affect anything currently, previously i was using these to generate the invoices but now, they have no influence so don't waste your time to test them or something like that). The delivery date is the date where the client is expected to receive the order (of course the order will not be sent to the client if it's not confirmed, ), and based on that date the business staff will take some decisions. for example if the order is in far state (like Medenine) and the delivery date is tomorrow, the staff must prepare the order today and give him to the shipping company, so it will be sent to warehouse of medenine today (the admin must do that because if he give him tomorrow, it will not arrive tomorrow). so basically the delivery date is the date where the admin will give the order to the shipping company (he must do that before the delivery date), and the delivery date is the date where the client will receive the order. last but not least each state can have a default shipper, so when the client places an order from the storefront and chooses a state, if it has a default shipper automatically, if not it will be null until the admin edits the order and update it manually. Finally the acceptance dates, currently we have 2 columns "tunis_acceptance_delivery_date", "wilayet_acceptance_delivery_date", generally i'm targeting businesses that are on the "Grand Tunis Area" which are 4 states (Tunis, Ariana, Ben Arous and Mannouba), the businesses are used to ship the orders for this are with private shippers (individuals) so they can still accept the order today morning and ship it with the shipper afternoon for example, not like the far states (the other 20 states), these states generally must be shipped by a shipping company so we cannot accept an order for today for those areas. the orders for far states must be shipped to warehouses before at least 1 day. whether the orders in near places can be accepted and shipped on the same day, so i separated the acceptance date based on that. these dates will be used to set the delivery_date when a client places an order from the storefront. as we said early the delivery_date can later be update by the admin, but i'm using these settings now to add a default value to the delivery_date, the admin can update these settings anytime he want based on his business situation.
- **Behavior:** Admins can create, rename, and delete shippers; shippers assigned to existing orders are soft-deleted and unused shippers are removed. Admins can set each state's customer shipping fee in dinars (stored in millimes) and assign an optional default shipper. City options depend on the selected state, and locality options depend on the selected city. Storefront orders use the selected state's shipping fee and default shipper. The order delivery date comes from the Tunis acceptance date for Tunis, Ariana, Ben Arous, and Manouba, and from the wilayet acceptance date for other states. Both configured dates must be in the future. Delivery/return costs are not part of current business behavior.
- **Status:** [x] Specified · [x] Tests added · [x] Passing
- **Coverage:** `ShippingConfigurationTest` covers shipper CRUD and deletion behavior, state shipping-fee conversion/default-shipper assignment, dependent city/locality endpoints, future-date validation and settings updates, and storefront order fee/default-shipper/delivery-date selection for Tunis versus another state. Delivery and return costs are not tested, per the stated behavior.

#### A-08 Coupons

- **Scope:** Coupon CRUD, activation, validity dates, usage limits, discount type/value, and customer application.
- **Why:** this is a simple crud, each coupon has a code (name eg: bg10) the code is unique, the value is a number that defines the percentage discount, for example if the value is 10 and the order total is 100dt, the discount will be 10dt (10% of 100dt). if the value is 20 and the order total is 100dt, the discount will be 20dt (20% of 100dt), you get the point. and finally the status, only active codes are applicable. only the admin can create, update, delete coupons. but the storefront customer can use the coupon code if it's active (on the checkout page).
- **Behavior:** Admins can create coupons with a unique code, percentage value from 1 to 100, and active/inactive status. They can activate or deactivate coupons and delete them; coupons referenced by orders are soft-deleted, while unused coupons are permanently deleted. Only admins can access coupon management. Storefront lookup and checkout accept active coupons only; the percentage is applied to product totals before shipping is added.
- **Status:** [x] Specified · [x] Tests added · [x] Passing
- **Coverage:** `CouponManagementTest` covers creation, uniqueness and value/status validation, activation/deactivation, active-only storefront lookup, order-history-aware deletion, and admin-only access. `CheckoutPricingTest` covers active coupon discount calculation and rejects inactive coupons during order placement. Expiration dates and usage limits are not present in this project’s coupon model and are not tested.

#### A-09 Invoices

- **Scope:** Invoice creation/editing, order/client association, totals, and printable/downloadable output.
- **Why:** The invoices are simple than you mentionned on the scope, they are not associated withh orders or clients. they are not printable or downloadable. currently we are using them to calculate the expenses and revenues, each invoice has type (expense or revenue) and an amount, a unique title, category and an optional description. we are using these to calculate the earnings of the business. by deducting the expenses from the revenues. and to know what are the most revenuable/expensable categories.
-- Note : previously i was attachhing each delivered/returned order to an invoice (each order has his own invoice), but now, we are not doing that anymore, we are just creating invoices manually. so you'll notice that the model is still having an invoiceable method. you are free to delete this implementation and delete the Invoiceable model and the related migration.
- **Behavior:** Admins create and update standalone revenue/expense records with a unique title, category, amount entered in dinars and stored in millimes, and optional description. Invoices are not associated with orders or clients and have no print/download behavior. Only admins can manage them.
- **Status:** [x] Specified · [x] Tests added · [x] Passing
- **Coverage:** `InvoiceManagementTest` covers revenue/expense creation, dinar-to-millime conversion, optional descriptions, unique title and field validation, updates, filtered listing, details, deletion, and admin-only access. Print/download and order/client association behavior are excluded. Creating without a description exposed and fixed the controller’s assumption that the optional field was always present.

#### A-10 Reports and dashboard statistics

- **Scope:** Selling/shipping reports, date/filter calculations, exports, dashboard metrics, and cleanup commands.
- **Why:** 
-- Shipping reports : if you remember earlier we talked about shippers, i said that i'm generally targeting businesses that are located in "Great Tunis" (Tunis, Ariana, Ben Arous, Manouba), so generally business owners prefer to work with individual shippers to deliver in this area because it's much cheaper than a company, so if they will work with individual shippers, the business must provide the shipper the orders on his truck, and a list of the orders (shipping report),it's a document that contains all the confirmed orders with a specific delivery_date for that shipper (each shipper has a delivery area, it's not mentionned on the project, but the business staff know it). the shipping report, contains the id, client name, phone, address, products, and price, he should call each client on the list, gets more info from the client about the location and when he arrives to the client, it opens his truck, and get the order with the same id on the report and give it to the client. and mark the order as delivered (just on paper), and for canceled orders by clients, he marks them as returned in paper and bring them back to the business, and for the orders that are not delivered for any reason (the client didn't answer the phone, the client changed his mind, the address is wrong, the client is not at home, the order is broken on the way, ...etc), he must inform the business about the situation. so the business can contact the client and figure out what's going on. and generally there delivery_date will be updated to tomorrow so we give them a second chance to get the order. each shipping reports must have 2 files, 1 excel file that contains all the confirmed orders with a specific delivery_date, and pdf file that contains the same data but formated as invoice to be printed as invoices (labels) and pasted on the order package (using a sticky label paper).
-- Selling report : is a report related to products to know how many times each product(variant not the wrapper), appeared on the orders, and the sum of that appears, for example if a product appeared 1 time in an order and his quantity is 3, so the value of it on the selling report's quantity will be 3, and number of orders is 1. if the same product appeared 2 times in another order, so the selling report for that product will be 3+2=5 total quantity and 1+1=2 total number of orders. it's important to know that here i'm not focusing in specific status of orders it's just the total of all orders regardless of their status. we do that to know the most wanted products. This file must be exported as excel file
-- clean commands : currently i have only one clean command to clean the activity_log table, when it executed by the admin, all the records older than 365 days will be deleted. (implement for me other clean command to delete notifications and shipping reports(i want only to keep last 7 days notifications and shipping reports), for the shipping reports deletion i want to delete the records on the db and their excel and pdf files from storage).
-- dashboard metrics :  i didn't understand what you meant by that, after you complete the test and implementation let's talk about that.
- **Behavior:** A shipping report request requires a date with at least one confirmed order and queues report generation with the chosen shipper/date. A selling report requires a valid inclusive start/end date range and queues generation for the requesting user; its aggregation includes orders regardless of status and sums variant quantities plus distinct order counts. Cleanup keeps notifications and shipping/selling reports from the last seven days; older report rows and their associated Excel/PDF files are deleted. The dashboard shows seven daily counts including today: all orders placed by clients or recorded by admins, alongside a separate count of storefront orders placed by clients. A separate chart counts confirmed/shipped/delivered/returned orders, canceled orders, and distinct NRP orders by creation date, plus delivered and returned orders by delivery date. Both charts default to the current month's start and end dates and accept separate date filters. The comparison helps assess sales-campaign performance independently from media-platform metrics.
- **Status:** [x] Specified · [x] Tests added · [x] Passing
- **Coverage:** `ReportManagementTest` checks shipping and selling report request dispatch/validation and all three seven-day cleanup commands, including retention of recent data and deletion of old Excel/PDF files. `DashboardMetricsTest` verifies the two seven-day order series, the order outcome and delivery charts with independent date filters, distinct NRP counting, and current-month filter defaults. Generated report file contents are not covered by tests yet.

#### A-11 Notifications and notes

- **Scope:** Notification listing/read state and notes attached to relevant records.
- **Why:** Currently we are using notifications for alerting admins that new orders are being placed. and to inform admins that their role changed, selling reports created or failed notifications, shipping report created or failed notifications, and when the sending the order info via api request to the shipping company is failed. 
-- Notes : currently each admin on the system can add notes to orders and clients, these notes each with the admin who created it, and the date it was created, the note is  visible to all admins. the notes are viewable on the order's details page, and on the client's details page. 
- **Behavior:** Notifications are listed and marked read for the authenticated user only, with all/read/unread filters; mark-all-read affects only that user's unread notifications. Admins can add notes to orders and clients, and other admins can view each note with its author and creation time on the relevant detail page.
- **Status:** [x] Specified · [x] Tests added · [x] Passing
- **Coverage:** `NotificationAndNoteTest` checks user-scoped notification listing and read actions, plus shared order/client notes and author visibility on both detail pages. Notification creation triggers and note editing/deletion are not covered in this feature slice.

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
