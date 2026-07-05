<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\Shipper;
use App\Models\Wrapper;
use Illuminate\Database\Eloquent\Factories\Factory;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $client = Client::factory()->create();
        $shipper = Shipper::factory()->create();
        return [
            'client_id' => $client->id,
            'client_name' => $client->name,
            'phone' => $client->phone,
            'address' => $client->address,
            'state_id' => $client->state_id,
            'city_id' => $client->city_id,
            'locality_id' => $client->locality_id,
            'coupon_code_id' => null,
            'shipper_id' => $shipper->id,
            'shipping_cost' => in_array($client->state_id, [1, 2, 3, 4]) ? 6000 : 7000,
            'status' => 'confirmed',
            'amount' => 1,
            'note' => null,
            'delivery_date' => now()->addDays(1),
            'created_at' => fake()->dateTimeBetween('2026-07-01', '2026-07-30')
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Order $order) {
            $products = Product::factory()->count(rand(1, 2))->create();
            $totalAmount = 0;

            $wrapper = Wrapper::factory()->create();


            foreach ($products as $index => $product) {

                $product->wrappers()->attach($wrapper->id, [
                    'display_order' => $index + 1,
                    'is_default' => $index == 0 ? true : false,
                    'free_shipping' => rand(0, 1),
                    'update_quantity' => 1,
                ]);







                $quantity = rand(1, 5);
                $subTotal = $product->price * $quantity;
                $totalAmount += $subTotal;

                OrderProduct::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'price' => $product->price,
                    'quantity' => $quantity,
                    'sub_total' => $subTotal
                ]);
            }

            $order->update(
                [
                    'amount' => $totalAmount + $order->shipping_cost
                ]
            );
        });
    }
}
