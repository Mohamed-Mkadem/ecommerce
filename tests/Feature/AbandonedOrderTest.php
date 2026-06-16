<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\State;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AbandonedOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_an_abandoned_order_from_phone_only_data(): void
    {
        $product = Product::factory()->create([
            'price' => 30000,
            'type' => 'product',
        ]);

        $response = $this->postJson(route('FE.orders.abandoned'), [
            'phone' => '23456789',
            'cart' => [[
                'id' => $product->id,
                'quantity' => 1,
                'price' => 30,
                'free_shipping' => false,
            ]],
            'total' => 30,
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('orders', [
            'phone' => '23456789',
            'status' => 'abandoned',
        ]);
    }

    public function test_it_reuses_an_abandoned_order_when_the_client_places_the_final_order(): void
    {
        $product = Product::factory()->create([
            'price' => 30000,
            'type' => 'product',
        ]);

        $state = State::factory()->create([
            'shipping_cost' => 7000,
        ]);

        $abandonedOrder = Order::factory()->create([
            'phone' => '23456789',
            'status' => 'abandoned',
            'state_id' => null,
            'address' => null,
            'client_name' => null,
            'amount' => null,
            'shipping_cost' => null,
            'delivery_date' => null,
        ]);

        $response = $this->postJson(route('FE.orders.place'), [
            'name' => 'Client Name',
            'address' => '123 Main Street',
            'phone' => '23456789',
            'state' => ['id' => $state->id, 'shipping_cost' => $state->shipping_cost],
            'coupon' => null,
            'cart' => [[
                'id' => $product->id,
                'quantity' => 1,
                'price' => 30,
                'free_shipping' => false,
            ]],
            'total' => 37,
            'note' => null,
            'free_shipping' => false,
        ]);

        $response->assertRedirect();

        $abandonedOrder->refresh();

        $this->assertSame('pending', $abandonedOrder->status);
        $this->assertSame('Client Name', $abandonedOrder->client_name);
        $this->assertSame('123 Main Street', $abandonedOrder->address);
        $this->assertSame(1, $abandonedOrder->products()->count());
    }
}
