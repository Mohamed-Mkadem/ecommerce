<?php

namespace Tests\Feature\Admin;

use App\Models\Order;
use App\Models\Product;
use App\Models\Shipper;
use App\Models\ShippingSetting;
use App\Models\State;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Queue;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ShippingConfigurationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Queue::fake();
    }

    public function test_admin_can_manage_shippers_and_deleting_an_assigned_shipper_preserves_it_soft_deleted(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin)->post(route('shippers.store'), ['name' => 'Sweetia Delivery'])
            ->assertRedirect();
        $shipper = Shipper::query()->where('name', 'Sweetia Delivery')->firstOrFail();

        $this->actingAs($admin)->postJson(route('shippers.store'), ['name' => 'Sweetia Delivery'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('name');

        $this->actingAs($admin)->put(route('shippers.update', $shipper), ['name' => 'Sweetia Express'])
            ->assertRedirect();
        $this->assertSame('Sweetia Express', $shipper->fresh()->name);

        $this->makeOrder($this->makeState(), $shipper);
        $this->actingAs($admin)->delete(route('shippers.destroy', $shipper))->assertRedirect();
        $this->assertSoftDeleted('shippers', ['id' => $shipper->id]);

        $unusedShipper = Shipper::query()->create(['name' => 'Unused Delivery']);
        $this->actingAs($admin)->delete(route('shippers.destroy', $unusedShipper))->assertRedirect();
        $this->assertDatabaseMissing('shippers', ['id' => $unusedShipper->id]);
    }

    public function test_admin_can_set_state_shipping_price_and_default_shipper(): void
    {
        $admin = $this->makeAdmin();
        $state = $this->makeState();
        $shipper = Shipper::query()->create(['name' => 'State Carrier']);

        $this->actingAs($admin)->put(route('states.update', $state), [
            'price' => 8.5,
            'default_shipper_id' => $shipper->id,
        ])->assertRedirect(route('states.index'));

        $this->assertDatabaseHas('states', [
            'id' => $state->id,
            'shipping_cost' => 8500,
            'default_shipper_id' => $shipper->id,
        ]);

        $this->actingAs($admin)->get(route('states.edit', $state))
            ->assertOk()
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component('Admin/States/Edit')
                    ->where('state.default_shipper_id', $shipper->id)
                    ->where('shippers.0.id', $shipper->id)
            );
    }

    public function test_location_endpoints_return_cities_for_a_state_and_localities_for_a_city(): void
    {
        $admin = $this->makeAdmin();
        $state = $this->makeState();
        $otherState = $this->makeState();
        $city = $state->cities()->create();
        $otherCity = $otherState->cities()->create();
        $locality = $city->localities()->forceCreate(['postal_code' => '1000']);
        $otherLocality = $otherCity->localities()->forceCreate(['postal_code' => '2000']);

        $this->actingAs($admin)->getJson(route('cities.index', $state))
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.id', $city->id);

        $this->actingAs($admin)->getJson(route('localities.index', $city))
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.id', $locality->id);

        $this->assertNotSame($otherLocality->id, $locality->id);
    }

    public function test_shipping_acceptance_dates_require_future_dates_and_can_be_updated(): void
    {
        $admin = $this->makeAdmin();
        $settings = ShippingSetting::query()->create([
            'tunis_acceptance_delivery_date' => now()->addDays(2)->toDateString(),
            'wilayet_acceptance_delivery_date' => now()->addDays(3)->toDateString(),
        ]);
        $tunisDate = now()->addDays(4)->toDateString();
        $wilayetDate = now()->addDays(5)->toDateString();

        $this->actingAs($admin)->put(route('shippingSettings.update', $settings->id), [
            'tunisDeliveryDate' => $tunisDate,
            'wilayetDeliveryDate' => $wilayetDate,
        ])->assertRedirect();

        $settings->refresh();
        $this->assertSame($tunisDate, $settings->tunis_acceptance_delivery_date);
        $this->assertSame($wilayetDate, $settings->wilayet_acceptance_delivery_date);

        $this->actingAs($admin)->putJson(route('shippingSettings.update', $settings->id), [
            'tunisDeliveryDate' => now()->subDay()->toDateString(),
            'wilayetDeliveryDate' => $wilayetDate,
        ])->assertUnprocessable()->assertJsonValidationErrors('tunisDeliveryDate');
    }

    public function test_storefront_order_uses_state_shipping_fee_default_shipper_and_region_delivery_date(): void
    {
        $localShipper = Shipper::query()->create(['name' => 'Grand Tunis Carrier']);
        $regionalShipper = Shipper::query()->create(['name' => 'Regional Carrier']);
        $tunis = $this->makeState('Tunis', 8000, $localShipper->id);
        $regional = $this->makeState('Sousse', 12000, $regionalShipper->id);
        $tunisDate = now()->addDays(2)->toDateString();
        $regionalDate = now()->addDays(4)->toDateString();
        ShippingSetting::query()->create([
            'tunis_acceptance_delivery_date' => $tunisDate,
            'wilayet_acceptance_delivery_date' => $regionalDate,
        ]);
        $product = Product::factory()->create(['price' => 40000]);

        $this->post(route('FE.orders.place'), $this->orderPayload($product, $tunis, 48, '23456789'))
            ->assertRedirect();
        $this->post(route('FE.orders.place'), $this->orderPayload($product, $regional, 52, '23556789'))
            ->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'phone' => '23456789',
            'state_id' => $tunis->id,
            'shipping_cost' => 8000,
            'shipper_id' => $localShipper->id,
            'delivery_date' => $tunisDate,
        ]);
        $this->assertDatabaseHas('orders', [
            'phone' => '23556789',
            'state_id' => $regional->id,
            'shipping_cost' => 12000,
            'shipper_id' => $regionalShipper->id,
            'delivery_date' => $regionalDate,
        ]);
    }

    private function orderPayload(Product $product, State $state, float $total, string $phone): array
    {
        return [
            'name' => 'Shipping Test Customer',
            'address' => '1 Test Street',
            'phone' => $phone,
            'state' => ['id' => $state->id, 'shipping_cost' => $state->shipping_cost / 1000],
            'cart' => [[
                'id' => $product->id,
                'quantity' => 1,
                'price' => $product->price / 1000,
                'free_shipping' => false,
            ]],
            'total' => $total,
            'free_shipping' => false,
        ];
    }

    private function makeOrder(State $state, Shipper $shipper): Order
    {
        $client = $state->clients()->create([
            'name' => 'Shipper Test Customer',
            'phone' => fake()->numerify('2#######'),
            'address' => 'Tunis',
        ]);

        return Order::query()->create([
            'client_id' => $client->id,
            'state_id' => $state->id,
            'shipper_id' => $shipper->id,
            'status' => 'confirmed',
            'source' => 'admin',
            'amount' => 35000,
            'shipping_cost' => 5000,
            'client_name' => $client->name,
            'address' => $client->address,
            'phone' => $client->phone,
            'delivery_date' => now()->addDay()->toDateString(),
        ]);
    }

    private function makeState(string $name = 'Test State', int $shippingCost = 5000, ?int $shipperId = null): State
    {
        return State::factory()->create([
            'shipping_cost' => $shippingCost,
            'default_shipper_id' => $shipperId,
            'en' => ['name' => $name],
            'fr' => ['name' => $name],
            'ar' => ['name' => 'ولاية اختبار'],
        ]);
    }

    private function makeAdmin(): User
    {
        return User::query()->create([
            'first_name' => 'Shipping',
            'last_name' => 'Admin',
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);
    }
}
