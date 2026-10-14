<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Nrp;
use App\Models\Order;
use App\Models\Product;
use App\Models\Shipper;
use App\Models\State;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ShowcaseOrdersSeeder extends Seeder
{
    /**
     * Seed clearly labelled demo orders without adding them to the default seeding flow.
     */
    public function run(): void
    {
        $state = State::query()->with('cities.localities')->first();

        if (! $state || ! $state->cities->first() || ! $state->cities->first()->localities->first()) {
            throw new RuntimeException('Seed states, cities, and localities before showcase orders.');
        }

        $city = $state->cities->first();
        $locality = $city->localities->first();
        $products = Product::query()
            ->whereHas('wrappers', fn ($query) => $query->whereIn('slug', [
                'tunisian-bsisa',
                'kaak-warka',
                'tunisian-zrir',
                'stuffed-dates',
                'cookies',
                'kaaber-carthage',
                'tunisian-samsa',
                'almond-baklawa',
            ]))
            ->with('wrappers')
            ->orderBy('products.id')
            ->get();

        if ($products->isEmpty()) {
            throw new RuntimeException('Run CatalogSeeder before ShowcaseOrdersSeeder.');
        }

        $plans = [
            ['day' => 9, 'client' => 1, 'status' => 'delivered'],
            ['day' => 9, 'client' => 2, 'status' => 'returned'],
            ['day' => 9, 'client' => 5, 'status' => 'canceled'],
            ['day' => 8, 'client' => 1, 'status' => 'delivered'],
            ['day' => 8, 'client' => 3, 'status' => 'canceled'],
            ['day' => 7, 'client' => 2, 'status' => 'delivered'],
            ['day' => 7, 'client' => 4, 'status' => 'delivered'],
            ['day' => 7, 'client' => 6, 'status' => 'pending', 'nrp_tries' => 1],
            ['day' => 6, 'client' => 5, 'status' => 'returned'],
            ['day' => 6, 'client' => 3, 'status' => 'delivered'],
            ['day' => 5, 'client' => 1, 'status' => 'delivered'],
            ['day' => 5, 'client' => 2, 'status' => 'returned'],
            ['day' => 4, 'client' => 7, 'status' => 'shipped'],
            ['day' => 4, 'client' => 8, 'status' => 'confirmed'],
            ['day' => 4, 'client' => 9, 'status' => 'pending', 'nrp_tries' => 2],
            ['day' => 3, 'client' => 4, 'status' => 'delivered'],
            ['day' => 3, 'client' => 5, 'status' => 'canceled'],
            ['day' => 2, 'client' => 6, 'status' => 'delivered'],
            ['day' => 2, 'client' => 10, 'status' => 'returned'],
            ['day' => 2, 'client' => 1, 'status' => 'confirmed'],
            ['day' => 1, 'client' => 2, 'status' => 'delivered'],
            ['day' => 1, 'client' => 3, 'status' => 'returned'],
            ['day' => 1, 'client' => 7, 'status' => 'pending', 'nrp_tries' => 3],
            ['day' => 0, 'client' => 8, 'status' => 'pending'],
            ['day' => 0, 'client' => 9, 'status' => 'abandoned'],
            ['day' => 0, 'client' => 10, 'status' => 'delivered'],
        ];

        DB::transaction(function () use ($state, $city, $locality, $products, $plans): void {
            $shippers = collect(['Intigo', 'Ahmed'])
                ->mapWithKeys(fn (string $name) => [$name => Shipper::firstOrCreate(['name' => $name])]);

            $clients = collect(range(1, 10))->mapWithKeys(function (int $number) use ($state, $city, $locality) {
                // Use visibly fictional, non-routable numbers for showcase-only clients.
                $phone = sprintf('000000%02d', $number);

                $client = Client::updateOrCreate(
                    ['phone' => $phone],
                    [
                        'name' => sprintf('Demo Client %02d', $number),
                        'state_id' => $state->id,
                        'city_id' => $city->id,
                        'locality_id' => $locality->id,
                        'address' => 'Showcase address',
                    ],
                );

                return [$number => $client];
            });

            foreach ($plans as $index => $plan) {
                $orderNumber = $index + 1;
                $client = $clients[$plan['client']];
                $createdAt = Carbon::today()->subDays($plan['day'])->setTime(10 + ($index % 8), ($index * 7) % 60);
                $deliveryDate = in_array($plan['status'], ['delivered', 'returned'], true)
                    ? $createdAt->toDateString()
                    : $createdAt->copy()->addDay()->toDateString();
                $note = sprintf('SHOWCASE DATA: demo order %02d', $orderNumber);
                $shipperName = $index % 2 === 0 ? 'Intigo' : 'Ahmed';
                $freeShipping = $index % 8 === 0;
                $shippingCost = $freeShipping ? 0 : (int) $state->shipping_cost;

                $order = Order::firstOrNew(['note' => $note]);
                $order->fill([
                    'client_id' => $client->id,
                    'state_id' => $state->id,
                    'city_id' => $city->id,
                    'locality_id' => $locality->id,
                    'shipper_id' => $shippers[$shipperName]->id,
                    'status' => $plan['status'],
                    'source' => $index % 2 === 0 ? 'client' : 'admin',
                    'amount' => 0,
                    'shipping_cost' => $shippingCost,
                    'free_shipping' => $freeShipping,
                    'client_name' => $client->name,
                    'address' => $client->address,
                    'phone' => $client->phone,
                    'delivery_date' => $deliveryDate,
                ]);
                $order->created_at = $createdAt;
                $order->save();

                $product = $products[$index % $products->count()];
                $quantityStep = (float) ($product->wrappers->first()?->pivot?->update_quantity ?? 1);
                $quantity = 1 + ($index % 3 * $quantityStep);
                $subtotal = (int) round($product->price * $quantity);
                $order->amount = $subtotal + $shippingCost;
                $order->saveQuietly();
                $order->products()->sync([
                    $product->id => [
                        'price' => $product->price,
                        'quantity' => $quantity,
                        'sub_total' => $subtotal,
                    ],
                ]);

                if (isset($plan['nrp_tries'])) {
                    $nrp = Nrp::firstOrNew(['order_id' => $order->id]);
                    $nrp->tries = $plan['nrp_tries'];
                    $nrp->created_at = $createdAt;
                    $nrp->save();
                } else {
                    $order->nrp()->delete();
                }
            }

            $clients->each->recalculateDeliveryRate();
        });
    }
}
