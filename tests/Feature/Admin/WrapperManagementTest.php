<?php

namespace Tests\Feature\Admin;

use App\Models\Product;
use App\Models\User;
use App\Models\Wrapper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class WrapperManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_a_translated_wrapper_with_a_default_variant_and_image(): void
    {
        Storage::fake('public');
        $admin = $this->makeAdmin();
        $default = Product::factory()->create(['price' => 70000]);
        $other = Product::factory()->create(['price' => 39000]);

        $this->actingAs($admin)->post(route('wrappers.store'), $this->wrapperPayload([
            $this->variant($default, 0, isDefault: true, step: 0.5),
            $this->variant($other, 1, freeShipping: true),
        ], includeImage: true))->assertRedirect(route('wrappers.create'));

        $wrapper = Wrapper::query()->firstOrFail();
        $this->assertSame('almond-baklawa', $wrapper->slug);
        $this->assertSame('Almond Baklawa', $wrapper->translate('en')->title);
        $this->assertSame('Baklawa aux amandes', $wrapper->translate('fr')->title);
        $this->assertSame('بقلاوة اللوز', $wrapper->translate('ar')->title);
        $this->assertSame($default->id, $wrapper->defaultProduct()->id);
        $this->assertSame(70000, $wrapper->defaultProduct()->price);
        $this->assertSame(0.5, (float) $wrapper->defaultProduct()->pivot->update_quantity);
        $this->assertTrue((bool) $wrapper->products()->whereKey($other->id)->first()->pivot->free_shipping);
        $this->assertCount(1, $wrapper->getMedia('images'));

        $this->actingAs($admin)->get(route('wrappers.show', $wrapper))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Wrappers/Show')
                ->where('wrapper.price', '70.000')
                ->where('wrapper.default_product.id', $default->id)
            );
    }

    public function test_wrapper_creation_requires_exactly_one_default_variant(): void
    {
        $admin = $this->makeAdmin();
        $product = Product::factory()->create();
        $payload = $this->wrapperPayload([
            $this->variant($product, 0, isDefault: false),
        ], includeImage: true);

        $this->actingAs($admin)->from(route('wrappers.create'))
            ->post(route('wrappers.store'), $payload)
            ->assertSessionHasErrors('products');

        $this->assertSame(0, Wrapper::count());

        $anotherProduct = Product::factory()->create();
        $payload['products'] = [
            $this->variant($product, 0, isDefault: true),
            $this->variant($anotherProduct, 1, isDefault: true),
        ];

        $this->actingAs($admin)->from(route('wrappers.create'))
            ->post(route('wrappers.store'), $payload)
            ->assertSessionHasErrors('products');

        $this->assertSame(0, Wrapper::count());
    }

    public function test_a_variant_already_attached_to_another_wrapper_cannot_be_added_on_create(): void
    {
        $admin = $this->makeAdmin();
        $alreadyOwned = Product::factory()->create();
        $available = Product::factory()->create();
        $this->makeWrapper('existing-wrapper')->products()->attach($alreadyOwned->id, [
            'display_order' => 0,
            'is_default' => true,
            'free_shipping' => false,
            'update_quantity' => 1,
        ]);

        $this->actingAs($admin)->from(route('wrappers.create'))
            ->post(route('wrappers.store'), $this->wrapperPayload([
                $this->variant($alreadyOwned, 0, isDefault: true),
                $this->variant($available, 1),
            ], includeImage: true))
            ->assertSessionHasErrors('products.0.product_id');

        $this->assertSame(1, Wrapper::count());
    }

    public function test_edit_form_offers_owned_and_unattached_variants_but_excludes_other_owners(): void
    {
        $admin = $this->makeAdmin();
        $wrapper = $this->makeWrapper('target-wrapper');
        $owned = Product::factory()->create(['price' => 20000]);
        $unattached = Product::factory()->create(['price' => 30000]);
        $ownedByOther = Product::factory()->create(['price' => 40000]);
        $wrapper->products()->attach($owned->id, [
            'display_order' => 0,
            'is_default' => true,
            'free_shipping' => false,
            'update_quantity' => 1,
        ]);
        $this->makeWrapper('another-wrapper')->products()->attach($ownedByOther->id, [
            'display_order' => 0,
            'is_default' => true,
            'free_shipping' => false,
            'update_quantity' => 1,
        ]);

        $this->actingAs($admin)->get(route('wrappers.edit', $wrapper))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Wrappers/Edit')
                ->has('products', 2)
                ->where('products.0.id', $owned->id)
                ->where('products.1.id', $unattached->id)
            );
    }

    public function test_update_can_replace_variants_and_save_their_pivot_options(): void
    {
        $admin = $this->makeAdmin();
        $wrapper = $this->makeWrapper('update-target');
        $previous = Product::factory()->create();
        $selected = Product::factory()->create(['price' => 50000]);
        $wrapper->products()->attach($previous->id, [
            'display_order' => 0,
            'is_default' => true,
            'free_shipping' => false,
            'update_quantity' => 1,
        ]);
        $payload = $this->wrapperPayload([
            $this->variant($selected, 0, isDefault: true, freeShipping: true, step: 0.5),
        ]);
        $payload['en']['title'] = 'Updated Almond Baklawa';

        $this->actingAs($admin)->put(route('wrappers.update', $wrapper), $payload)
            ->assertRedirect(route('wrappers.show', $wrapper->fresh()));

        $wrapper->refresh();
        $this->assertSame('updated-almond-baklawa', $wrapper->slug);
        $this->assertSame('Updated Almond Baklawa', $wrapper->translate('en')->title);
        $this->assertDatabaseMissing('product_wrapper', [
            'wrapper_id' => $wrapper->id,
            'product_id' => $previous->id,
        ]);
        $this->assertDatabaseHas('product_wrapper', [
            'wrapper_id' => $wrapper->id,
            'product_id' => $selected->id,
            'is_default' => true,
            'free_shipping' => true,
            'update_quantity' => 0.5,
        ]);
    }

    public function test_variant_owned_by_another_wrapper_cannot_be_added_during_update(): void
    {
        $admin = $this->makeAdmin();
        $wrapper = $this->makeWrapper('update-target');
        $otherWrapper = $this->makeWrapper('other-owner');
        $owned = Product::factory()->create();
        $otherOwned = Product::factory()->create();
        $wrapper->products()->attach($owned->id, [
            'display_order' => 0,
            'is_default' => true,
            'free_shipping' => false,
            'update_quantity' => 1,
        ]);
        $otherWrapper->products()->attach($otherOwned->id, [
            'display_order' => 0,
            'is_default' => true,
            'free_shipping' => false,
            'update_quantity' => 1,
        ]);

        $this->actingAs($admin)->from(route('wrappers.edit', $wrapper))
            ->put(route('wrappers.update', $wrapper), $this->wrapperPayload([
                $this->variant($otherOwned, 0, isDefault: true),
            ]))
            ->assertSessionHasErrors('products.0.product_id');

        $this->assertDatabaseHas('product_wrapper', [
            'wrapper_id' => $wrapper->id,
            'product_id' => $owned->id,
        ]);
    }

    public function test_deleting_a_wrapper_detaches_its_variants_without_deleting_them(): void
    {
        $admin = $this->makeAdmin();
        $wrapper = $this->makeWrapper('delete-target');
        $product = Product::factory()->create();
        $wrapper->products()->attach($product->id, [
            'display_order' => 0,
            'is_default' => true,
            'free_shipping' => false,
            'update_quantity' => 1,
        ]);

        $this->actingAs($admin)->delete(route('wrappers.destroy', $wrapper))
            ->assertRedirect(route('wrappers.index'));

        $this->assertDatabaseMissing('wrappers', ['id' => $wrapper->id]);
        $this->assertDatabaseMissing('product_wrapper', ['wrapper_id' => $wrapper->id]);
        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }

    public function test_moderator_can_view_wrappers_but_cannot_access_mutation_pages_or_actions(): void
    {
        $moderator = $this->makeUser('moderator');
        $wrapper = $this->makeWrapper('moderator-visible');

        $this->actingAs($moderator)->get(route('wrappers.index'))->assertOk();
        $this->actingAs($moderator)->get(route('wrappers.show', $wrapper))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Wrappers/Show')
                ->where('auth.user.role', 'moderator')
            );

        $this->actingAs($moderator)->get(route('wrappers.create'))->assertForbidden();
        $this->actingAs($moderator)->get(route('wrappers.edit', $wrapper))->assertForbidden();
        $this->actingAs($moderator)->postJson(route('wrappers.store'), [])
            ->assertForbidden();
        $this->actingAs($moderator)->putJson(route('wrappers.update', $wrapper), [])
            ->assertForbidden();
        $this->actingAs($moderator)->deleteJson(route('wrappers.destroy', $wrapper))
            ->assertForbidden();
    }

    private function wrapperPayload(array $products, bool $includeImage = false): array
    {
        $payload = [
            'caption' => 'A family favorite',
            'is_active' => true,
            'en' => ['title' => 'Almond Baklawa', 'description' => 'Made for sharing.'],
            'fr' => ['title' => 'Baklawa aux amandes', 'description' => 'À partager.'],
            'ar' => ['title' => 'بقلاوة اللوز', 'description' => 'للمشاركة.'],
            'products' => $products,
        ];

        if ($includeImage) {
            $payload['images'] = [UploadedFile::fake()->image('wrapper.jpg')];
        }

        return $payload;
    }

    private function variant(
        Product $product,
        int $order,
        bool $isDefault = false,
        bool $freeShipping = false,
        float $step = 1,
    ): array {
        return [
            'product_id' => $product->id,
            'display_order' => $order,
            'is_default' => $isDefault,
            'free_shipping' => $freeShipping,
            'update_quantity' => $step,
        ];
    }

    private function makeWrapper(string $slug): Wrapper
    {
        return Wrapper::factory()->create([
            'slug' => $slug,
            'en' => ['title' => 'Wrapper ' . $slug, 'description' => null],
            'fr' => ['title' => 'Wrapper ' . $slug, 'description' => null],
            'ar' => ['title' => 'Wrapper ' . $slug, 'description' => null],
        ]);
    }

    private function makeAdmin(): User
    {
        return $this->makeUser('admin');
    }

    private function makeUser(string $role): User
    {
        return User::create([
            'first_name' => 'Wrapper',
            'last_name' => 'Admin',
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'role' => $role,
            'status' => 'active',
        ]);
    }
}
