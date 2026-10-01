<?php

namespace Tests\Feature\Admin;

use App\Models\Client;
use App\Models\City;
use App\Models\Order;
use App\Models\Product;
use App\Models\Shipper;
use App\Models\State;
use App\Models\Locality;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class OrderManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_mark_an_order_nrp_and_each_follow_up_call_increments_tries(): void
    {
        $admin = $this->makeAdmin();
        $order = $this->makeOrder();

        $this->actingAs($admin)->post(route('nrp.store', $order))->assertRedirect();
        $this->assertDatabaseHas('nrps', ['order_id' => $order->id, 'tries' => 1]);

        $this->actingAs($admin)->post(route('nrp.store', $order))->assertRedirect();
        $this->assertDatabaseHas('nrps', ['order_id' => $order->id, 'tries' => 2]);
    }

    public function test_nrp_orders_are_hidden_from_main_orders_and_visible_in_nrp_list(): void
    {
        $admin = $this->makeAdmin();
        $visibleOrder = $this->makeOrder();
        $nrpOrder = $this->makeOrder();
        $nrpOrder->nrp()->create(['tries' => 3]);

        $this->actingAs($admin)->get(route('orders.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Orders/Index')
                ->where('orders.data.0.id', $visibleOrder->id)
                ->has('orders.data', 1)
            );

        $this->actingAs($admin)->get(route('nrp.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/NRPs/Index')
                ->where('nrps.data.0.order_id', $nrpOrder->id)
                ->where('nrps.data.0.tries', 3)
            );
    }

    public function test_confirming_or_canceling_an_nrp_order_removes_it_from_the_nrp_list(): void
    {
        $admin = $this->makeAdmin();
        $order = $this->makeOrder();
        $order->nrp()->create(['tries' => 3]);

        $this->actingAs($admin)->patch(route('orders.updateStatus', $order), ['status' => 'confirmed'])
            ->assertRedirect();

        $this->assertSame('confirmed', $order->fresh()->status);
        $this->assertDatabaseMissing('nrps', ['order_id' => $order->id]);

        $order->nrp()->create(['tries' => 1]);
        $this->actingAs($admin)->patch(route('orders.updateStatus', $order), ['status' => 'canceled'])
            ->assertRedirect();

        $this->assertSame('canceled', $order->fresh()->status);
        $this->assertDatabaseMissing('nrps', ['order_id' => $order->id]);
    }

    public function test_admin_can_update_order_products_and_recalculate_the_order_amount(): void
    {
        $admin = $this->makeAdmin();
        $order = $this->makeOrder();
        $previousProduct = Product::factory()->create(['price' => 30000]);
        $replacement = Product::factory()->create(['price' => 25000]);
        $order->products()->attach($previousProduct->id, [
            'price' => 30000,
            'quantity' => 1,
            'sub_total' => 30000,
        ]);

        $this->actingAs($admin)->post(route('orders.updateProducts'), [
            'order_id' => $order->id,
            'cart' => [['id' => $replacement->id, 'quantity' => 2, 'price' => '25.000']],
            'total' => 50,
        ])->assertRedirect(route('orders.show', $order));

        $this->assertSame(55000, $order->fresh()->amount); // 50.000 products + 5.000 shipping
        $this->assertDatabaseMissing('order_product', [
            'order_id' => $order->id,
            'product_id' => $previousProduct->id,
        ]);
        $this->assertDatabaseHas('order_product', [
            'order_id' => $order->id,
            'product_id' => $replacement->id,
            'price' => 25000,
            'quantity' => 2,
            'sub_total' => 50000,
        ]);
    }

    public function test_admin_can_update_order_and_client_information_and_free_shipping(): void
    {
        $admin = $this->makeAdmin();
        $order = $this->makeOrder();
        $state = State::factory()->create(['shipping_cost' => 9000]);
        $city = City::query()->create(['state_id' => $state->id]);
        $locality = Locality::query()->forceCreate(['city_id' => $city->id, 'postal_code' => '1000']);
        $shipper = Shipper::query()->create(['name' => 'Updated Delivery']);
        $deliveryDate = now()->addDays(3)->toDateString();

        $this->actingAs($admin)->put(route('orders.update', $order), [
            'shipper' => $shipper->id,
            'deliveryDate' => $deliveryDate,
            'name' => 'Updated Customer',
            'address' => 'New delivery address',
            'phone' => '23123456',
            'phone2' => '25123456',
            'state' => $state->id,
            'city' => $city->id,
            'locality' => $locality->id,
            'free_shipping' => true,
        ])->assertRedirect(route('orders.show', $order));

        $order->refresh();
        $this->assertSame('Updated Customer', $order->client_name);
        $this->assertSame('23123456', $order->phone);
        $this->assertSame('25123456', $order->phone2);
        $this->assertSame('New delivery address', $order->address);
        $this->assertSame($deliveryDate, $order->delivery_date);
        $this->assertSame($state->id, $order->state_id);
        $this->assertSame($city->id, $order->city_id);
        $this->assertSame($locality->id, $order->locality_id);
        $this->assertSame($shipper->id, $order->shipper_id);
        $this->assertTrue((bool) $order->free_shipping);
        $this->assertSame(30000, $order->amount); // The previous shipping cost is removed.

        $this->assertDatabaseHas('clients', [
            'id' => $order->client_id,
            'name' => 'Updated Customer',
            'phone' => '23123456',
            'phone2' => '25123456',
            'address' => 'New delivery address',
            'state_id' => $state->id,
            'city_id' => $city->id,
            'locality_id' => $locality->id,
        ]);
    }

    private function makeOrder(): Order
    {
        $state = State::factory()->create();
        $shipper = Shipper::query()->create(['name' => 'Local Delivery']);
        $client = Client::query()->create([
            'name' => 'Sweetia Customer',
            'phone' => '22123456',
            'address' => 'Tunis',
            'state_id' => $state->id,
        ]);

        return Order::query()->create([
            'client_id' => $client->id,
            'state_id' => $state->id,
            'status' => 'pending',
            'amount' => 35000,
            'shipping_cost' => 5000,
            'shipper_id' => $shipper->id,
            'client_name' => $client->name,
            'address' => $client->address,
            'phone' => $client->phone,
            'delivery_date' => now()->addDay()->toDateString(),
            'source' => 'admin',
        ]);
    }

    private function makeAdmin(): User
    {
        return User::query()->create([
            'first_name' => 'Order',
            'last_name' => 'Admin',
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);
    }
}
