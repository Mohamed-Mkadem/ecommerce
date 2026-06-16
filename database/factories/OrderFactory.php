<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Client;
use App\Models\Product;
use App\Models\Shipper;
use App\Models\OrderProduct;
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
            'coupon_code_id' => null,
            'shipper_id' => $shipper->id,
            'shipping_cost' => in_array($client->state_id, [1, 2, 3, 4]) ? 6000 : 7000,
            'status' => 'confirmed',
            'amount' => 1,
            'note' => null,
            'delivery_date' => now()->addDays(1)
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Order $order) {
            $products = Product::factory()->count(rand(1, 4))->create();
            $totalAmount = 0;

            foreach ($products as $product) {
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
