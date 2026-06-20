# Re-Architecting Products & Wrappers

This plan outlines the steps to adjust the original Product and Wrapper architecture according to your new requirements. We will simplify Products by removing images and specific columns, and enhance Wrappers with translations and images. Finally, we will configure a custom quantity increment value (`update_quantity`) for each product variant attached to a wrapper.

## User Review Required
> [!IMPORTANT]
> The `title` and `description` columns will be removed from the `wrappers` table and moved to a new `wrapper_translations` table. This means any existing data in the `title` and `description` columns for wrappers in the database will be lost unless we migrate it. Please let me know if you need to preserve existing wrapper titles/descriptions, or if it is safe to drop those columns and start fresh!

## Proposed Changes

### Database Migrations
Create a migration to perform the following:
- **`products` table**: Drop `type` and `ends_at` columns.
- **`product_wrapper` pivot table**: Add `update_quantity` column (`double`, default: `1`).
- **`wrapper_translations` table**: Create this table to hold translations for `title` and `description` for `fr`, `ar`, and `en`.
- **`wrappers` table**: Drop `title` and `description` columns (now handled by the translation table).

---

### Backend Models

#### [MODIFY] `app/Models/Product.php`
- Remove the `HasMedia` and `InteractsWithMedia` traits.
- Remove the `registerMediaCollections` method.
- Remove `type` and `ends_at` from `$fillable`.
- Update the `wrappers()` relationship to include `update_quantity` in `withPivot`.

#### [MODIFY] `app/Models/Wrapper.php`
- Add the `HasMedia` and `InteractsWithMedia` traits, and the `registerMediaCollections` method.
- Add the `Astrotomic\Translatable\Translatable` trait to support translations.
- Set `$translatedAttributes` to `['title', 'description']`.
- Update the `products()` relationship to include `update_quantity` in `withPivot`.

#### [MODIFY] `app/Models/ProductWrapper.php`
- Add `update_quantity` to the `$fillable` array.

---

### Controllers & Requests

#### [MODIFY] `app/Http/Controllers/ProductController.php` & `app/Http/Requests/Product/`
- Remove logic for handling image uploads.
- Remove `type` and `ends_at` validation and assignment.

#### [MODIFY] `app/Http/Controllers/WrapperController.php` & `app/Http/Requests/Wrapper/`
- Add logic to handle multi-language inputs for `title` and `description`.
- Add logic to handle media uploads (images) for the Wrapper.
- When attaching products to a wrapper, ensure `update_quantity` is validated and stored.

#### [MODIFY] `app/Http/Resources/`
- Update `ProductResource` and `WrapperResource` to reflect the new structure (e.g., media moved to wrappers, translations moved to wrappers, `update_quantity` included in pivot data).

---

### Frontend Views (Vue 3)

#### [MODIFY] `resources/js/Pages/Admin/Products/` (`Create.vue`, `Edit.vue`, `Index.vue`)
- Remove the image upload section.
- Remove the `type` and `ends_at` fields.

#### [MODIFY] `resources/js/Pages/Admin/Wrappers/` (`Create.vue`, `Edit.vue`, `WrapperCard.vue`)
- Add multi-language inputs for `title` and `description` (`fr`, `ar`, `en`).
- Add the image upload section (similar to what Products previously had).
- In the "Attach Products" section, add an input to define the `update_quantity` for each attached product.

#### [MODIFY] `resources/js/Pages/FrontEnd/Wrapper.vue`
- Update the quantity selector logic to increment/decrement by the `update_quantity` defined on the selected product variant pivot.

## Verification Plan

### Automated Tests
- Run `php artisan migrate` to ensure the database schema updates without errors.

### Manual Verification
- Test creating a product without images, `type`, or `ends_at`.
- Test creating a Wrapper, ensuring you can upload images, fill out translations in 3 languages, and assign products with specific `update_quantity` values.
- Verify the frontend storefront correctly displays Wrapper images/translations and that the quantity selector steps correctly for different variants.
