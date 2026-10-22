<?php

namespace Tests\Feature\Admin;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ProductManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_a_product_with_all_localized_variant_names(): void
    {
        $admin = $this->makeUser('admin');

        $this->actingAs($admin)->post(route('products.store'), $this->validProductData())
            ->assertRedirect();

        $product = Product::query()->firstOrFail();
        $this->assertSame(32500, $product->price);
        $this->assertSame('Shipping Almond Box', $product->shipping_name);
        $this->assertSame('Almond Baklawa 1kg', $product->translate('en')->name);
        $this->assertSame('Baklawa amande 1kg', $product->translate('fr')->name);
        $this->assertSame('بقلاوة لوز 1كغ', $product->translate('ar')->name);
        $this->assertSame(10, (int) $product->discount);
        $this->assertSame('percentage', $product->discount_type);
    }

    public function test_product_creation_requires_price_shipping_name_and_each_locale(): void
    {
        $admin = $this->makeUser('admin');
        $invalid = $this->validProductData();
        unset($invalid['ar']['name']);
        $invalid['price'] = 0;
        $invalid['shipping_name'] = '';

        $this->actingAs($admin)->postJson(route('products.store'), $invalid)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['price', 'shipping_name', 'ar.name']);

        $this->assertSame(0, Product::count());
    }

    public function test_admin_can_update_price_discount_and_translations(): void
    {
        $admin = $this->makeUser('admin');
        $product = Product::factory()->create(['price' => 25000]);
        $updated = $this->validProductData();
        $updated['price'] = 42;
        $updated['en']['name'] = 'Updated Almond Box';
        $updated['discount'] = 5;
        $updated['discount_type'] = 'fixed';

        $this->actingAs($admin)->put(route('products.update', $product), $updated)
            ->assertRedirect();

        $product->refresh();
        $this->assertSame(42000, $product->price);
        $this->assertSame('Updated Almond Box', $product->translate('en')->name);
        $this->assertSame(5, (int) $product->discount);
        $this->assertSame('fixed', $product->discount_type);
    }

    public function test_moderator_can_view_product_pages_but_cannot_submit_product_changes(): void
    {
        $moderator = $this->makeUser('moderator');
        $product = Product::factory()->create();

        $this->actingAs($moderator)->get(route('products.index'))->assertOk();
        $this->actingAs($moderator)->get(route('products.create'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Admin/Products/Create'));
        $this->actingAs($moderator)->get(route('products.edit', $product))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Admin/Products/Edit'));
        $this->actingAs($moderator)->postJson(route('products.store'), $this->validProductData())
            ->assertForbidden();
        $this->actingAs($moderator)->putJson(route('products.update', $product), $this->validProductData())
            ->assertForbidden();
    }

    public function test_product_list_search_uses_the_current_locale(): void
    {
        $admin = $this->makeUser('admin');
        $matching = Product::factory()->create([
            'price' => 30000,
            'en' => ['name' => 'Almond Pastry'],
            'fr' => ['name' => 'Pâtisserie aux amandes'],
            'ar' => ['name' => 'حلوى اللوز'],
        ]);
        Product::factory()->create([
            'price' => 30000,
            'en' => ['name' => 'Pistachio Pastry'],
            'fr' => ['name' => 'Pâtisserie à la pistache'],
            'ar' => ['name' => 'حلوى بالفستق'],
        ]);

        $this->actingAs($admin)
            ->withSession(['locale' => 'fr'])
            ->get(route('products.index', ['search' => 'amandes']))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Products/Index')
                ->where('products.data.0.id', $matching->id)
                ->where('products.data.0.name', 'Pâtisserie aux amandes')
                ->where('filters.search', 'amandes')
            );
    }

    public function test_product_without_order_history_is_permanently_deleted(): void
    {
        $admin = $this->makeUser('admin');
        $product = Product::factory()->create();
        $productId = $product->id;

        $this->actingAs($admin)->delete(route('products.destroy', $product))
            ->assertRedirect(route('products.index'));

        $this->assertDatabaseMissing('products', ['id' => $productId]);
        $this->assertDatabaseMissing('product_translations', ['product_id' => $productId]);
    }

    private function validProductData(): array
    {
        return [
            'price' => 32.5,
            'shipping_name' => 'Shipping Almond Box',
            'discount' => 10,
            'discount_type' => 'percentage',
            'en' => ['name' => 'Almond Baklawa 1kg'],
            'fr' => ['name' => 'Baklawa amande 1kg'],
            'ar' => ['name' => 'بقلاوة لوز 1كغ'],
        ];
    }

    private function makeUser(string $role): User
    {
        return User::create([
            'first_name' => 'Product',
            'last_name' => 'Tester',
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'role' => $role,
            'status' => 'active',
        ]);
    }
}
