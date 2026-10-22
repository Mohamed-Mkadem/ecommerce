# Sweetia Project Context

Last updated: 2026-10-22

This document is a handoff summary for future development and support conversations. It describes the Sweetia repository and deployment from the project state inspected on the date above. Check the code and deployment environment if a detail may have changed. Never put secrets or production `.env` values in this file.

## What Sweetia is

Sweetia is a multilingual ecommerce and order-management platform for Tunisian pastry shops. It includes a public storefront for customers and an administrative workspace for managing the catalog, orders, customers, delivery, finances, staff, and business reports. It is currently deployed as a demo at `https://sweetia.mohamad.tn` and is intended to showcase a reusable project that can be adapted for future clients.

The domain is a subdomain of `mohamad.tn`; DNS for the domain remains with the existing registrar/hosting provider, while the Sweetia application runs on a separate OVH VPS.

## Main features

### Customer storefront

- Home, about, contact, privacy, and terms pages.
- Product and wrapper catalog, product detail pages, and product images managed through Laravel Media Library.
- Product variants with prices and wrapper relationships. Wrapper main images are used for product cards/details where available, with a placeholder fallback.
- Default-product discounts support fixed-value and percentage display.
- Shopping cart, checkout/order form, delivery location selection, and coupon application.
- English, French, and Arabic interface/localized content, including RTL support for Arabic.
- Optional Google Analytics 4 tracking. Meta/Facebook tracking is controlled by `FACEBOOK_ENABLED` and is currently intended to remain disabled for the demo. Do not copy credentials from the Boughanmi project.

### Admin workspace

- Dashboard with recent order charts (15-day order and storefront-order charts), operational summaries, and order-rate visualizations.
- Order creation and lifecycle management, order details/activity, status changes, shipping assignment, and non-delivery/return handling.
- Spreadsheet order imports and bulk updates for order data such as status, pending state, shipper, delivery date, and amount.
- Product, variant, wrapper, media, coupon, client, location/state, shipping setting, shipper, and staff management.
- Client search, filters, details, spending/order statistics, and spreadsheet import.
- Invoice records for revenue and expenses, with categories and earnings statistics.
- Admin notifications, activity history, employee profiles/roles, and notes.
- Statistics pages for earnings, orders, products, clients, and coupon codes.
- Shipping reports and selling reports with downloadable files and cleanup of reports older than seven days.
- Order Reports: admins create a uniquely named date-range report. Generation is queued; the Excel file contains date, total orders, admin orders, and storefront orders. Reports can be downloaded/deleted, and reports older than seven days can be cleaned up. A completion notification is broadcast to admins.
- Scheduled cleanup commands for notifications and report files. Current scheduled retention is seven days; verify `routes/console.php` before changing retention.

### Roles and access

- User roles include `admin` and `moderator`; users can also have active/banned status.
- Authentication and active-account middleware protect the admin area. Sensitive management/report routes use admin middleware.
- Private broadcast notification channels are authorized for admins and moderators. Inspect route middleware and policies before changing access behavior.

## Technical summary

### Backend

- Laravel 12 (`laravel/framework` `^12.0`).
- Composer requires PHP `^8.4.1`.
- MySQL database; production Sweetia uses its own database and application credentials.
- Inertia Laravel 2 connects the Laravel backend to the Vue frontend.
- Laravel Reverb, Laravel Echo, and Pusher JS provide real-time broadcast notifications.
- Database-backed queues handle background jobs and queued report/order integrations.
- Laravel notifications, database migrations/seeders, and scheduled Artisan commands are used throughout.
- Laravel Media Library manages uploaded product and user media.
- Maatwebsite Excel handles spreadsheet imports/exports; Spatie PDF is used for PDF report output.
- Spatie Activity Log records administrative activity.
- Astrotomic Laravel Translatable supports translated models/content.
- Integrations/configuration include GA4, optional Meta/Facebook Conversion API tracking, and shipping-provider jobs (DSGO, Navex, Onesta). Values belong in environment configuration, not source control.

### Frontend

- Vue 3 Composition API, Inertia Vue 3, Vite, Tailwind CSS, and Sass.
- Chart.js / vue-chartjs, Pinia, Vue I18n, Flatpickr, Tiptap, SweetAlert2, and Vue Toastification are among the UI libraries.
- The Vite entry and page imports are organized for chunked page bundles. Remixicon is installed as an npm dependency; avoid reintroducing the giant full icon-font SVG as a bundled asset.
- Inertia UI Modal is installed from the Laravel package's local Vue package at `vendor/inertiaui/modal/vue`.

## Tests and seeders

- PHP tests are run through `composer test` / `scripts/run-tests.php`.
- Puppeteer browser workflows are available as `npm run test:e2e:admin-order` and `npm run test:e2e:storefront-order`.
- `CatalogSeeder` and its JSON/assets seed reusable product and wrapper data. `StateSeeder` supplies Tunisian delivery locations. `ShowcaseOrdersSeeder` creates sample order history for demonstrating charts; review before running it on a non-demo database.
- Seeders that create demo/admin data must be used deliberately on production. Never reset production tables to seed demo content.

## Sweetia demo deployment

- Host: OVH VPS, Ubuntu 26.04.
- Public hostname: `sweetia.mohamad.tn`; Nginx and Certbot provide HTTPS.
- Runtime: PHP 8.4, Composer, Node.js 22, MySQL 8.4.
- Laravel queues use `QUEUE_CONNECTION=database` and run under Supervisor (`sweetia-worker`).
- Reverb listens on localhost port 8080 under systemd (`sweetia-reverb`); Nginx proxies WebSocket traffic.
- Production checkout: `/var/www/sweetia`, owned by `deploy`.
- Automated deployment is defined in `.github/workflows/deploy-production.yml`. It currently deploys pushes to `codex/laravel-12-upgrade`; it is not yet triggered by `dev` or `main`. Do not change the watched branch without coordinating the workflow and server checkout.
- `scripts/deploy-production.sh` makes a backup, installs locked Composer/npm dependencies, builds Vite assets, enters maintenance mode, runs forced migrations, rebuilds Laravel caches, restarts queue workers and restores the site.
- `scripts/backup-sweetia.sh` creates database and uploads archives, keeps local backups, and uploads encrypted copies to Google Drive using rclone. A deploy-user cron runs it daily at 03:00 server time; deployment also runs a backup.
- `.env`, SSH keys, Google OAuth secrets/tokens, database passwords, analytics secrets, and rclone encryption passwords are private deployment data. Never commit or paste their values into project documentation.
- The deployment plan and current operational sequence are recorded in `tasks.md`.

## Important project separation

Sweetia and Boughanmi Patisserie are separate projects with separate databases, VPSs, branches, analytics credentials, and deployment settings. The recent Hostinger PHP2Shell remediation discussion concerns the **Boughanmi Hostinger/CloudPanel VPS**, not the Sweetia OVH VPS. On Boughanmi, PHP 8.2 was upgraded to 8.2.35; PHP 8.3 FPM was reported active and the user was advised to disable it because the site uses PHP 8.2. The user has not yet confirmed that the disable command completed. Do not assume Boughanmi server state is Sweetia server state.

## Working conventions

- Preserve the current deployed branch workflow unless the user explicitly asks to promote or change branches.
- Do not run tests unless the user asks to test/verify, but if they ask, use the documented commands above.
- For production changes, prepare the concrete commands/change first, explain any service interruption or data impact, and guide the user one step at a time when they are operating the VPS themselves.
- Keep keys, secrets, database contents, and private customer data out of commits and handoff files.
- For current behavior, inspect the relevant Laravel controller/model, Vue page, route, migration, and seeder rather than relying only on this summary.
