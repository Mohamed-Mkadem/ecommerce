<?php

namespace Tests\Feature\FrontEnd;

use App\Models\Product;
use App\Models\Wrapper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class StorefrontPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_lists_active_wrappers_with_the_current_locale_and_default_product(): void
    {
        $wrapper = $this->wrapperWithProduct(
            slug: 'sweetia-favorites',
            titles: ['en' => 'Sweetia Favorites', 'fr' => 'Les préférés Sweetia', 'ar' => 'حلويات سويتيا'],
            price: 24500,
        );
        $this->wrapperWithProduct(
            slug: 'hidden-wrapper',
            titles: ['en' => 'Hidden', 'fr' => 'Masqué', 'ar' => 'مخفي'],
            price: 10000,
            active: false,
        );
        Wrapper::factory()->create([
            'slug' => 'empty-wrapper',
            'is_active' => true,
            'en' => ['title' => 'Empty', 'description' => null],
            'fr' => ['title' => 'Vide', 'description' => null],
            'ar' => ['title' => 'فارغ', 'description' => null],
        ]);

        $this->withSession(['locale' => 'fr'])
            ->get(route('FE.home'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('FrontEnd/Home')
                ->has('wrappers', 1)
                ->where('wrappers.0.id', $wrapper->id)
                ->where('wrappers.0.title', 'Les préférés Sweetia')
                ->where('wrappers.0.price', '24.500')
                ->where('wrappers.0.default_product.id', $wrapper->products()->first()->id)
            );
    }

    public function test_shop_search_uses_the_current_wrapper_translation(): void
    {
        $matching = $this->wrapperWithProduct(
            slug: 'gateau-aux-amandes',
            titles: ['en' => 'Almond Pastry', 'fr' => 'Gâteau aux amandes', 'ar' => 'حلوى اللوز'],
            price: 25000,
        );
        $this->wrapperWithProduct(
            slug: 'pistachio-pastry',
            titles: ['en' => 'Pistachio Pastry', 'fr' => 'Pâtisserie à la pistache', 'ar' => 'حلوى بالفستق'],
            price: 30000,
        );

        $this->withSession(['locale' => 'fr'])
            ->get(route('FE.shop', ['search' => 'amandes']))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('FrontEnd/Shop')
                ->where('wrappers.data.0.id', $matching->id)
                ->where('wrappers.data.0.title', 'Gâteau aux amandes')
                ->where('filters.search', 'amandes')
            );
    }

    public function test_shop_sorts_wrappers_by_their_default_variant_price_and_omits_unavailable_wrappers(): void
    {
        $low = $this->wrapperWithProduct(
            slug: 'low-price',
            titles: ['en' => 'Low price', 'fr' => 'Petit prix', 'ar' => 'سعر منخفض'],
            price: 12000,
        );
        $high = $this->wrapperWithProduct(
            slug: 'high-price',
            titles: ['en' => 'High price', 'fr' => 'Prix élevé', 'ar' => 'سعر مرتفع'],
            price: 45000,
        );
        $this->wrapperWithProduct(
            slug: 'inactive-wrapper',
            titles: ['en' => 'Inactive', 'fr' => 'Inactif', 'ar' => 'غير نشط'],
            price: 20000,
            active: false,
        );
        Wrapper::factory()->create([
            'slug' => 'no-products',
            'is_active' => true,
            'en' => ['title' => 'No products', 'description' => null],
            'fr' => ['title' => 'Sans produits', 'description' => null],
            'ar' => ['title' => 'بدون منتجات', 'description' => null],
        ]);

        $this->get(route('FE.shop', ['sort' => 'highest_price']))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('FrontEnd/Shop')
                ->has('wrappers.data', 2)
                ->where('wrappers.data.0.id', $high->id)
                ->where('wrappers.data.1.id', $low->id)
                ->where('filters.sort', 'highest_price')
            );
    }

    public function test_wrapper_page_shows_variants_and_falls_back_to_default_for_an_unattached_variant(): void
    {
        $wrapper = Wrapper::factory()->create([
            'slug' => 'family-assortment',
            'is_active' => true,
            'en' => ['title' => 'Family Assortment', 'description' => 'Made for sharing'],
            'fr' => ['title' => 'Assortiment familial', 'description' => 'À partager'],
            'ar' => ['title' => 'تشكيلة عائلية', 'description' => 'للمشاركة'],
        ]);
        $default = Product::factory()->create(['price' => 30000]);
        $second = Product::factory()->create(['price' => 50000]);
        $unattached = Product::factory()->create(['price' => 70000]);
        $wrapper->products()->attach($default->id, [
            'display_order' => 0,
            'is_default' => true,
            'free_shipping' => false,
            'update_quantity' => 0.5,
        ]);
        $wrapper->products()->attach($second->id, [
            'display_order' => 1,
            'is_default' => false,
            'free_shipping' => true,
            'update_quantity' => 1,
        ]);

        $this->get(route('FE.wrapper', ['wrapper' => $wrapper->slug, 'variant' => $unattached->id]))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('FrontEnd/Wrapper')
                ->where('wrapper.title', 'Family Assortment')
                ->has('variants', 2)
                ->where('selected_product_id', $default->id)
                ->where('variants.0.id', $default->id)
                ->where('variants.0.is_default', true)
                ->where('variants.0.update_quantity', 0.5)
                ->where('variants.1.id', $second->id)
                ->where('variants.1.free_shipping', true)
            );
    }

    public function test_inactive_or_empty_wrappers_are_not_available_as_public_detail_pages(): void
    {
        $inactive = $this->wrapperWithProduct(
            slug: 'inactive-detail',
            titles: ['en' => 'Inactive', 'fr' => 'Inactif', 'ar' => 'غير نشط'],
            price: 20000,
            active: false,
        );
        $empty = Wrapper::factory()->create([
            'slug' => 'empty-detail',
            'is_active' => true,
            'en' => ['title' => 'Empty', 'description' => null],
            'fr' => ['title' => 'Vide', 'description' => null],
            'ar' => ['title' => 'فارغ', 'description' => null],
        ]);

        $this->get(route('FE.wrapper', $inactive->slug))->assertNotFound();
        $this->get(route('FE.wrapper', $empty->slug))->assertNotFound();
    }

    public function test_about_contact_terms_and_privacy_routes_render_their_inertia_pages(): void
    {
        foreach ([
            ['FE.about', 'FrontEnd/About'],
            ['FE.contact', 'FrontEnd/Contact'],
            ['FE.terms', 'FrontEnd/Terms'],
            ['FE.privacy', 'FrontEnd/Privacy'],
        ] as [$route, $component]) {
            $this->get(route($route))
                ->assertOk()
                ->assertInertia(fn (Assert $page) => $page->component($component));
        }
    }

    public function test_language_switcher_stores_supported_locale_and_rejects_unknown_locale(): void
    {
        $this->get('/lang/ar')
            ->assertRedirect()
            ->assertSessionHas('locale', 'ar');

        $this->get('/lang/xx')
            ->assertRedirect()
            ->assertSessionHas('locale', 'ar');
    }

    private function wrapperWithProduct(
        string $slug,
        array $titles,
        int $price,
        bool $active = true,
    ): Wrapper {
        $wrapper = Wrapper::factory()->create([
            'slug' => $slug,
            'is_active' => $active,
            'en' => ['title' => $titles['en'], 'description' => null],
            'fr' => ['title' => $titles['fr'], 'description' => null],
            'ar' => ['title' => $titles['ar'], 'description' => null],
        ]);
        $product = Product::factory()->create(['price' => $price]);
        $wrapper->products()->attach($product->id, [
            'display_order' => 0,
            'is_default' => true,
            'free_shipping' => false,
            'update_quantity' => 1,
        ]);

        return $wrapper;
    }
}
