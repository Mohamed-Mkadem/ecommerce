<?php

namespace Tests\Feature\Admin;

use App\Models\City;
use App\Models\Client;
use App\Models\Locality;
use App\Models\Nrp;
use App\Models\Order;
use App\Models\State;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class DashboardMetricsTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_shows_all_orders_and_storefront_orders_for_the_last_seven_days(): void
    {
        $admin = User::query()->create([
            'first_name' => 'Dashboard',
            'last_name' => 'Admin',
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $state = State::factory()->create();
        $city = City::query()->create(['state_id' => $state->id]);
        $locality = Locality::query()->forceCreate(['city_id' => $city->id, 'postal_code' => '1000']);
        $client = Client::query()->create([
            'name' => 'Dashboard Client',
            'phone' => '22123456',
            'address' => 'Test address',
            'state_id' => $state->id,
            'city_id' => $city->id,
            'locality_id' => $locality->id,
        ]);

        $this->createOrder($client, $state, $city, $locality, 'client', now());
        $this->createOrder($client, $state, $city, $locality, 'admin', now());
        $this->createOrder($client, $state, $city, $locality, 'client', now()->subDays(8));

        $this->actingAs($admin)->get(route('dashboard'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Dashboard')
                ->where('weeklyOrders.6.date', now()->toDateString())
                ->where('weeklyOrders.6.count', 2)
                ->where('weeklyClientOrders.6.date', now()->toDateString())
                ->where('weeklyClientOrders.6.count', 1)
                ->where('weeklyOrders.0.date', now()->subDays(6)->toDateString())
                ->where('weeklyOrders.0.count', 0)
                ->has('weeklyOrders', 7)
                ->has('weeklyClientOrders', 7));
    }

    public function test_charts_data_counts_order_outcomes_and_delivery_results_with_separate_date_filters(): void
    {
        $admin = $this->makeAdmin();
        [$client, $state, $city, $locality] = $this->makeClientLocation();

        $this->createOrder($client, $state, $city, $locality, 'client', '2026-05-05 10:00:00', 'confirmed', '2026-06-03');
        $this->createOrder($client, $state, $city, $locality, 'admin', '2026-05-06 10:00:00', 'shipped', '2026-06-03');
        $delivered = $this->createOrder($client, $state, $city, $locality, 'client', '2026-05-07 10:00:00', 'delivered', '2026-06-04');
        $returned = $this->createOrder($client, $state, $city, $locality, 'admin', '2026-05-08 10:00:00', 'returned', '2026-06-05');
        $this->createOrder($client, $state, $city, $locality, 'admin', '2026-05-09 10:00:00', 'canceled', '2026-06-06');
        $nrpOrder = $this->createOrder($client, $state, $city, $locality, 'client', '2026-05-10 10:00:00', 'pending', '2026-06-06');
        $nrpOrder->nrp()->forceCreate(['tries' => 1, 'created_at' => '2026-05-10 11:00:00', 'updated_at' => '2026-05-10 11:00:00']);
        $nrpOrder->nrp()->forceCreate(['tries' => 2, 'created_at' => '2026-05-11 11:00:00', 'updated_at' => '2026-05-11 11:00:00']);

        // Delivery dates are the filter for the delivery chart, regardless of when the order was created.
        $this->createOrder($client, $state, $city, $locality, 'client', '2026-04-20 10:00:00', 'delivered', '2026-06-07');
        $this->createOrder($client, $state, $city, $locality, 'client', '2026-04-29 10:00:00', 'returned', '2026-05-31');
        $outsideRangeNrp = $this->createOrder($client, $state, $city, $locality, 'client', '2026-04-30 10:00:00', 'pending', '2026-06-08');
        $outsideRangeNrp->nrp()->forceCreate(['tries' => 1, 'created_at' => '2026-04-30 11:00:00', 'updated_at' => '2026-04-30 11:00:00']);

        $response = $this->actingAs($admin)->get(route('charts.update', [
            'rates_start_date' => '2026-05-01',
            'rates_end_date' => '2026-05-31',
            'delivery_start_date' => '2026-06-01',
            'delivery_end_date' => '2026-06-10',
        ]))->assertOk();

        $data = $response->json();
        $this->assertSame(4, $data['orderRates']['order.confirmed']);
        $this->assertSame(1, $data['orderRates']['order.canceled']);
        $this->assertSame(1, $data['orderRates']['order.nrp']);
        $this->assertSame(2, $data['deliveryRates']['order.delivered']);
        $this->assertSame(1, $data['deliveryRates']['order.returned']);
        $this->assertSame('2026-05-01', $data['ratesFilters']['start_date']);
        $this->assertSame('2026-05-31', $data['ratesFilters']['end_date']);
        $this->assertSame('2026-06-01', $data['deliveryFilters']['start_date']);
        $this->assertSame('2026-06-10', $data['deliveryFilters']['end_date']);
    }

    public function test_charts_data_defaults_both_periods_to_the_current_month(): void
    {
        $this->actingAs($this->makeAdmin())->get(route('charts.update'))
            ->assertOk()
            ->assertJsonPath('ratesFilters.start_date', now()->startOfMonth()->toDateString())
            ->assertJsonPath('ratesFilters.end_date', now()->endOfMonth()->toDateString())
            ->assertJsonPath('deliveryFilters.start_date', now()->startOfMonth()->toDateString())
            ->assertJsonPath('deliveryFilters.end_date', now()->endOfMonth()->toDateString());
    }

    private function createOrder(Client $client, State $state, City $city, Locality $locality, string $source, $createdAt, string $status = 'pending', ?string $deliveryDate = null): Order
    {
        return Order::query()->forceCreate([
            'client_id' => $client->id,
            'client_name' => $client->name,
            'phone' => $client->phone,
            'address' => $client->address,
            'state_id' => $state->id,
            'city_id' => $city->id,
            'locality_id' => $locality->id,
            'status' => $status,
            'source' => $source,
            'amount' => 10000,
            'shipping_cost' => 0,
            'delivery_date' => $deliveryDate ?? now()->addDay()->toDateString(),
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);
    }

    private function makeClientLocation(): array
    {
        $state = State::factory()->create();
        $city = City::query()->create(['state_id' => $state->id]);
        $locality = Locality::query()->forceCreate(['city_id' => $city->id, 'postal_code' => '1000']);
        $client = Client::query()->create([
            'name' => 'Dashboard Client',
            'phone' => '22123456',
            'address' => 'Test address',
            'state_id' => $state->id,
            'city_id' => $city->id,
            'locality_id' => $locality->id,
        ]);

        return [$client, $state, $city, $locality];
    }

    private function makeAdmin(): User
    {
        return User::query()->create([
            'first_name' => 'Dashboard',
            'last_name' => 'Admin',
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);
    }
}
