<?php

namespace Tests\Feature\Admin;

use App\Models\City;
use App\Models\Client;
use App\Models\Locality;
use App\Models\Note;
use App\Models\Order;
use App\Models\State;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ClientManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_a_client_with_an_optional_secondary_phone(): void
    {
        $admin = $this->makeAdmin();
        $location = $this->makeLocation();

        $this->actingAs($admin)->post(route('clients.store'), $this->clientPayload($location, [
            'name' => 'New Customer',
            'phone' => '22123456',
            'phone2' => '25123456',
        ]))->assertRedirect();

        $this->assertDatabaseHas('clients', [
            'name' => 'New Customer',
            'phone' => '22123456',
            'phone2' => '25123456',
            'state_id' => $location['state']->id,
            'city_id' => $location['city']->id,
            'locality_id' => $location['locality']->id,
            'address' => '12 Test Street',
        ]);

        $withoutPhone2 = $this->clientPayload($location, [
            'name' => 'Customer Without Secondary Phone',
            'phone' => '23123456',
        ]);
        unset($withoutPhone2['phone2']);
        $this->actingAs($admin)->post(route('clients.store'), $withoutPhone2)->assertRedirect();
        $this->assertDatabaseHas('clients', ['phone' => '23123456', 'phone2' => null]);
    }

    public function test_client_creation_requires_all_fields_except_phone2_and_a_unique_valid_phone(): void
    {
        $admin = $this->makeAdmin();
        $location = $this->makeLocation();
        $existing = $this->makeClient($location, '22123456');
        $invalid = $this->clientPayload($location, [
            'name' => '',
            'phone' => $existing->phone,
            'phone2' => '81234567',
            'address' => '',
        ]);

        $this->actingAs($admin)->postJson(route('clients.store'), $invalid)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'phone', 'phone2', 'address']);

        $this->assertSame(1, Client::query()->count());
    }

    public function test_admin_can_update_client_information_without_changing_their_phone(): void
    {
        $admin = $this->makeAdmin();
        $oldLocation = $this->makeLocation();
        $newLocation = $this->makeLocation();
        $client = $this->makeClient($oldLocation, '22123456');

        $this->actingAs($admin)->put(route('clients.update', $client), $this->clientPayload($newLocation, [
            'name' => 'Updated Customer',
            'phone' => '22123456',
            'phone2' => '27123456',
            'address' => 'Updated address',
        ]))->assertRedirect();

        $this->assertDatabaseHas('clients', [
            'id' => $client->id,
            'name' => 'Updated Customer',
            'phone' => '22123456',
            'phone2' => '27123456',
            'address' => 'Updated address',
            'state_id' => $newLocation['state']->id,
            'city_id' => $newLocation['city']->id,
            'locality_id' => $newLocation['locality']->id,
        ]);
    }

    public function test_admin_can_filter_clients_by_name_phone_and_state(): void
    {
        $admin = $this->makeAdmin();
        $location = $this->makeLocation();
        $matching = $this->makeClient($location, '22123456', [
            'name' => 'Almond Customer',
            'phone2' => '25123456',
        ]);
        $this->makeClient($this->makeLocation(), '23123456', ['name' => 'Pistachio Customer']);

        $this->actingAs($admin)->get(route('clients.index', [
            'search' => 'Almond',
            'phone' => '2512',
            'state' => $location['state']->id,
        ]))->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Clients/Index')
                ->where('clients.data.0.id', $matching->id)
                ->where('clients.data.0.name', 'Almond Customer')
                ->has('clients.data', 1)
            );
    }

    public function test_client_details_include_their_orders_and_notes(): void
    {
        $admin = $this->makeAdmin();
        $location = $this->makeLocation();
        $client = $this->makeClient($location, '22123456');
        $order = Order::query()->create([
            'client_id' => $client->id,
            'state_id' => $location['state']->id,
            'city_id' => $location['city']->id,
            'locality_id' => $location['locality']->id,
            'status' => 'delivered',
            'source' => 'admin',
            'amount' => 44000,
            'shipping_cost' => 4000,
            'client_name' => $client->name,
            'address' => $client->address,
            'phone' => $client->phone,
            'delivery_date' => now()->addDay()->toDateString(),
        ]);
        Note::query()->create([
            'content' => 'Prefers afternoon delivery',
            'user_id' => $admin->id,
            'notable_id' => $client->id,
            'notable_type' => Client::class,
        ]);

        $this->actingAs($admin)->get(route('clients.show', $client))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Clients/Show')
                ->where('client.id', $client->id)
                ->where('client.name', $client->name)
                ->where('orders.data.0.id', $order->id)
                ->where('notes.data.0.content', 'Prefers afternoon delivery')
            );
    }

    private function makeLocation(): array
    {
        $state = State::factory()->create();
        $city = City::query()->create(['state_id' => $state->id]);
        $locality = Locality::query()->forceCreate([
            'city_id' => $city->id,
            'postal_code' => '1000',
        ]);

        return compact('state', 'city', 'locality');
    }

    private function makeClient(array $location, string $phone, array $overrides = []): Client
    {
        return Client::query()->create(array_merge([
            'name' => 'Test Customer',
            'phone' => $phone,
            'phone2' => null,
            'address' => '12 Test Street',
            'state_id' => $location['state']->id,
            'city_id' => $location['city']->id,
            'locality_id' => $location['locality']->id,
        ], $overrides));
    }

    private function clientPayload(array $location, array $overrides = []): array
    {
        return array_merge([
            'name' => 'Test Customer',
            'phone' => '22123456',
            'phone2' => null,
            'address' => '12 Test Street',
            'state' => $location['state']->id,
            'city' => $location['city']->id,
            'locality' => $location['locality']->id,
        ], $overrides);
    }

    private function makeAdmin(): User
    {
        return User::query()->create([
            'first_name' => 'Client',
            'last_name' => 'Admin',
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);
    }
}
