<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\Client;
use App\Models\State;
use App\Models\ShippingSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class AbandonedOrderTest extends TestCase
{
    use RefreshDatabase;

    private State $state;

    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();

        $this->state = State::factory()->create([
            'shipping_cost' => 7000,
            'en' => ['name' => 'Tunis'],
            'fr' => ['name' => 'Tunis'],
            'ar' => ['name' => 'تونس'],
        ]);

        ShippingSetting::create([
            'tunis_acceptance_delivery_date' => '2026-10-02',
            'wilayet_acceptance_delivery_date' => '2026-10-03',
        ]);
    }

    public function test_it_creates_an_abandoned_order_from_phone_only_data(): void
    {
        $product = Product::factory()->create([
            'price' => 30000,
        ]);

        $response = $this->postJson(route('FE.orders.abandoned'), [
            'phone' => '23456789',
            'state' => ['id' => $this->state->id, 'shipping_cost' => 7],
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
        ]);

        $client = Client::create([
            'name' => 'Client Name',
            'phone' => '23456789',
            'address' => 'Old address',
            'state_id' => $this->state->id,
        ]);

        $abandonedOrder = Order::create([
            'client_id' => $client->id,
            'phone' => '23456789',
            'status' => 'abandoned',
            'state_id' => $this->state->id,
            'address' => 'Old address',
            'client_name' => 'Client Name',
            'amount' => null,
            'shipping_cost' => null,
            'delivery_date' => null,
        ]);

        $response = $this->postJson(route('FE.orders.place'), [
            'name' => 'Client Name',
            'address' => '123 Main Street',
            'phone' => '23456789',
            'state' => ['id' => $this->state->id, 'shipping_cost' => 7],
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
