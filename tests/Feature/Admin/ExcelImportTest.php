<?php

namespace Tests\Feature\Admin;

use App\Models\Client;
use App\Models\Order;
use App\Models\Product;
use App\Models\Shipper;
use App\Models\State;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Excel as ExcelFormat;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class ExcelImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_import_order_ids_to_return_orders_to_pending(): void
    {
        $admin = $this->makeAdmin();
        $toReopen = $this->makeOrder('confirmed');
        $alreadyPending = $this->makeOrder('pending');
        $file = $this->spreadsheet([
            ['id'],
            [$toReopen->id],
            [$alreadyPending->id],
        ]);

        $this->actingAs($admin)->post(route('orders.pendingUpdate.import.store'), ['file' => $file])
            ->assertRedirect();

        $this->assertSame('pending', $toReopen->fresh()->status);
        $this->assertSame('pending', $alreadyPending->fresh()->status);
    }

    public function test_admin_can_import_an_order_with_the_default_product_and_customer_product_note(): void
    {
        $admin = $this->makeAdmin();
        $state = State::factory()->create();
        $defaultProduct = Product::factory()->create(['price' => 42000]);
        config(['services.defult_product_id' => $defaultProduct->id]);
        $file = $this->spreadsheet([
            ['client_name', 'phone', 'state_id', 'address', 'products'],
            ['Imported Customer', '22111222', $state->id, 'Sousse', 'Two almond boxes'],
        ]);

        $this->actingAs($admin)->post(route('orders.excel.import.store'), ['file' => $file])
            ->assertRedirect();

        $client = Client::query()->where('phone', '22111222')->firstOrFail();
        $order = Order::query()->where('client_id', $client->id)->firstOrFail();
        $this->assertSame('pending', $order->status);
        $this->assertSame('Imported Customer', $order->client_name);
        $this->assertSame('Sousse', $order->address);
        $this->assertDatabaseHas('order_product', [
            'order_id' => $order->id,
            'product_id' => $defaultProduct->id,
            'quantity' => 1,
            'price' => 42000,
        ]);
        $this->assertSame('Two almond boxes', $order->notes()->firstOrFail()->content);
    }

    public function test_admin_can_import_shipping_status_updates_for_confirmed_or_shipped_orders(): void
    {
        $admin = $this->makeAdmin();
        $shipper = Shipper::query()->create(['name' => 'Import Carrier']);
        $confirmed = $this->makeOrder('confirmed');
        $confirmed->update(['shipper_id' => $shipper->id]);
        $shipped = $this->makeOrder('shipped');
        $shipped->update(['shipper_id' => $shipper->id]);
        $pending = $this->makeOrder('pending');
        $canceled = $this->makeOrder('confirmed');
        $canceled->update(['shipper_id' => $shipper->id]);
        $shippedStatus = $this->makeOrder('confirmed');
        $shippedStatus->update(['shipper_id' => $shipper->id]);
        $file = $this->spreadsheet([
            ['id', 'statut'],
            [$confirmed->id, 'delivered'],
            [$shipped->id, 'returned'],
            [$pending->id, 'delivered'],
            [$canceled->id, 'canceled'],
            [$shippedStatus->id, 'shipped'],
        ]);

        $this->actingAs($admin)->post(route('orders.import.store'), ['file' => $file])
            ->assertRedirect();

        $this->assertSame('delivered', $confirmed->fresh()->status);
        $this->assertSame('returned', $shipped->fresh()->status);
        $this->assertSame('pending', $pending->fresh()->status);
        $this->assertSame('canceled', $canceled->fresh()->status);
        $this->assertSame('shipped', $shippedStatus->fresh()->status);
    }

    public function test_admin_can_import_a_delivery_date_for_multiple_orders(): void
    {
        $admin = $this->makeAdmin();
        $first = $this->makeOrder('confirmed');
        $second = $this->makeOrder('shipped');
        $deliveryDate = now()->addDays(5)->toDateString();
        $file = $this->spreadsheet([
            ['id'],
            [$first->id],
            [$second->id],
        ]);

        $this->actingAs($admin)->post(route('orders.deliveryDate.import.store'), [
            'file' => $file,
            'date' => $deliveryDate,
        ])->assertRedirect();

        $this->assertSame($deliveryDate, $first->fresh()->delivery_date);
        $this->assertSame($deliveryDate, $second->fresh()->delivery_date);
    }

    public function test_admin_can_import_shipper_assignments(): void
    {
        $admin = $this->makeAdmin();
        $order = $this->makeOrder('confirmed');
        $shipper = Shipper::query()->create(['name' => 'Assigned Carrier']);
        $file = $this->spreadsheet([
            ['id', 'livreur'],
            [$order->id, $shipper->id],
        ]);

        $this->actingAs($admin)->post(route('orders.shipperUpdate.import.store'), ['file' => $file])
            ->assertRedirect();

        $this->assertSame($shipper->id, $order->fresh()->shipper_id);
    }

    public function test_admin_can_import_order_amounts_in_dinars_and_store_them_as_millimes(): void
    {
        $admin = $this->makeAdmin();
        $order = $this->makeOrder('confirmed');
        $file = $this->spreadsheet([
            ['id', 'montant'],
            [$order->id, 42.5],
        ]);

        $this->actingAs($admin)->post(route('orders.amountUpdate.import.store'), ['file' => $file])
            ->assertRedirect();

        $this->assertSame(42500, $order->fresh()->amount);
    }

    public function test_client_import_saves_secondary_phone_and_skips_numbers_already_used_as_either_phone(): void
    {
        $admin = $this->makeAdmin();
        $state = State::factory()->create();
        Client::query()->create([
            'name' => 'Existing Client',
            'phone' => '22123456',
            'phone2' => '25123456',
            'address' => 'Tunis',
            'state_id' => $state->id,
        ]);
        $file = $this->spreadsheet([
            ['nom', 'mobile', 'mobile2', 'adresse', 'gouvernorat'],
            ['Imported Client', '27123456', '29123456', 'Sfax', $state->id],
            ['Duplicate Phone', '25123456', '', 'Tunis', $state->id],
            ['Duplicate Secondary', '29123456', '', 'Sousse', $state->id],
        ]);

        $this->actingAs($admin)->post(route('clients.import.store'), ['file' => $file])
            ->assertRedirect();

        $this->assertSame(2, Client::query()->count());
        $this->assertDatabaseHas('clients', [
            'name' => 'Imported Client',
            'phone' => '27123456',
            'phone2' => '29123456',
            'address' => 'Sfax',
            'state_id' => $state->id,
        ]);
    }

    private function makeOrder(string $status): Order
    {
        $state = State::factory()->create();
        $client = Client::query()->create([
            'name' => 'Import Customer',
            'phone' => fake()->numerify('2#######'),
            'address' => 'Tunis',
            'state_id' => $state->id,
        ]);

        return Order::query()->create([
            'client_id' => $client->id,
            'state_id' => $state->id,
            'status' => $status,
            'amount' => 35000,
            'shipping_cost' => 5000,
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
            'first_name' => 'Excel',
            'last_name' => 'Admin',
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);
    }

    private function spreadsheet(array $rows): UploadedFile
    {
        $export = new class($rows) implements FromArray {
            public function __construct(private array $rows) {}

            public function array(): array
            {
                return $this->rows;
            }
        };

        return UploadedFile::fake()->createWithContent(
            'orders.xlsx',
            Excel::raw($export, ExcelFormat::XLSX),
        );
    }
}
