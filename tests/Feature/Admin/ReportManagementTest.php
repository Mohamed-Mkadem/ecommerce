<?php

namespace Tests\Feature\Admin;

use App\Jobs\CreateShippingReport;
use App\Jobs\GenerateSellingReport;
use App\Models\Order;
use App\Models\Client;
use App\Models\City;
use App\Models\Locality;
use App\Models\State;
use App\Models\ShippingReport;
use App\Models\SellingReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ReportManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_request_shipping_report_for_a_date_with_confirmed_orders(): void
    {
        $admin = $this->makeUser('admin');
        $state = State::factory()->create();
        $city = City::query()->create(['state_id' => $state->id]);
        $locality = Locality::query()->forceCreate(['city_id' => $city->id, 'postal_code' => '1000']);
        $client = Client::query()->create([
            'name' => 'Report Client', 'phone' => '22123456', 'address' => 'Test address',
            'state_id' => $state->id, 'city_id' => $city->id, 'locality_id' => $locality->id,
        ]);
        Order::query()->create([
            'client_id' => $client->id, 'client_name' => $client->name, 'phone' => $client->phone,
            'address' => $client->address, 'state_id' => $state->id, 'city_id' => $city->id,
            'locality_id' => $locality->id, 'status' => 'confirmed', 'source' => 'admin',
            'amount' => 10000, 'shipping_cost' => 0, 'delivery_date' => '2026-10-10',
        ]);
        Queue::fake();

        $this->actingAs($admin)->post(route('shipping_reports.store'), [
            'name' => 'October 10 delivery',
            'date' => '2026-10-10',
            'shipper_id' => 'gt',
        ])->assertRedirect();

        Queue::assertPushed(CreateShippingReport::class, fn ($job) => $job->grandTunisShipper === true
            && $job->requestData['date'] === '2026-10-10'
            && $job->requestData['name'] === 'October 10 delivery');
    }

    public function test_shipping_report_requires_confirmed_orders_for_the_requested_date(): void
    {
        $admin = $this->makeUser('admin');
        Queue::fake();

        $this->actingAs($admin)->postJson(route('shipping_reports.store'), [
            'name' => 'Empty date report',
            'date' => '2026-10-10',
            'shipper_id' => 'gt',
        ])->assertUnprocessable()->assertJsonValidationErrors(['date.count']);

        Queue::assertNothingPushed();
    }

    public function test_admin_can_request_selling_report_and_date_range_must_be_valid(): void
    {
        $admin = $this->makeUser('admin');
        Queue::fake();

        $this->actingAs($admin)->post(route('selling_reports.store'), [
            'name' => 'October sales',
            'start_date' => '2026-10-01',
            'end_date' => '2026-10-31',
        ])->assertRedirect();

        Queue::assertPushed(GenerateSellingReport::class, fn ($job) => $job->userId === $admin->id
            && $job->requestData['start_date'] === '2026-10-01'
            && $job->requestData['end_date'] === '2026-10-31');

        Queue::fake();
        $this->actingAs($admin)->postJson(route('selling_reports.store'), [
            'name' => 'Invalid range',
            'start_date' => '2026-10-20',
            'end_date' => '2026-10-01',
        ])->assertUnprocessable()->assertJsonValidationErrors(['end_date']);
        Queue::assertNothingPushed();
    }

    public function test_notifications_prune_removes_records_older_than_seven_days_only(): void
    {
        $admin = $this->makeUser('admin');
        $oldId = $this->createNotification($admin, now()->subDays(8));
        $recentId = $this->createNotification($admin, now()->subDays(6));

        $this->artisan('notifications:prune')->assertSuccessful();

        $this->assertDatabaseMissing('notifications', ['id' => $oldId]);
        $this->assertDatabaseHas('notifications', ['id' => $recentId]);
    }

    public function test_shipping_reports_prune_removes_old_rows_and_both_files_but_keeps_recent_reports(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('reports/old.xlsx', 'old excel');
        Storage::disk('local')->put('reports/old.pdf', 'old pdf');
        Storage::disk('local')->put('reports/recent.xlsx', 'recent excel');
        Storage::disk('local')->put('reports/recent.pdf', 'recent pdf');

        $old = ShippingReport::query()->create([
            'name' => 'Old report', 'orders_count' => 1, 'date' => now()->subDays(8)->toDateString(),
            'excel_file_path' => 'reports/old.xlsx', 'pdf_file_path' => 'reports/old.pdf',
        ]);
        $old->forceFill(['created_at' => now()->subDays(8)])->save();
        $recent = ShippingReport::query()->create([
            'name' => 'Recent report', 'orders_count' => 1, 'date' => now()->subDays(2)->toDateString(),
            'excel_file_path' => 'reports/recent.xlsx', 'pdf_file_path' => 'reports/recent.pdf',
        ]);
        $recent->forceFill(['created_at' => now()->subDays(2)])->save();

        $this->artisan('shipping-reports:prune')->assertSuccessful();

        $this->assertDatabaseMissing('shipping_reports', ['id' => $old->id]);
        $this->assertDatabaseHas('shipping_reports', ['id' => $recent->id]);
        Storage::disk('local')->assertMissing('reports/old.xlsx');
        Storage::disk('local')->assertMissing('reports/old.pdf');
        Storage::disk('local')->assertExists('reports/recent.xlsx');
        Storage::disk('local')->assertExists('reports/recent.pdf');
    }

    public function test_selling_reports_prune_removes_old_rows_and_both_files_but_keeps_recent_reports(): void
    {
        $admin = $this->makeUser('admin');
        Storage::fake('local');
        Storage::disk('local')->put('reports/old-selling.xlsx', 'old excel');
        Storage::disk('local')->put('reports/old-selling.pdf', 'old pdf');
        Storage::disk('local')->put('reports/recent-selling.xlsx', 'recent excel');
        Storage::disk('local')->put('reports/recent-selling.pdf', 'recent pdf');

        $old = SellingReport::query()->create([
            'name' => 'Old selling report',
            'user_id' => $admin->id,
            'start_date' => now()->subDays(10)->toDateString(),
            'end_date' => now()->subDays(8)->toDateString(),
            'excel_file_path' => 'reports/old-selling.xlsx',
            'pdf_file_path' => 'reports/old-selling.pdf',
        ]);
        $old->forceFill(['created_at' => now()->subDays(8)])->save();

        $recent = SellingReport::query()->create([
            'name' => 'Recent selling report',
            'user_id' => $admin->id,
            'start_date' => now()->subDays(4)->toDateString(),
            'end_date' => now()->subDays(2)->toDateString(),
            'excel_file_path' => 'reports/recent-selling.xlsx',
            'pdf_file_path' => 'reports/recent-selling.pdf',
        ]);
        $recent->forceFill(['created_at' => now()->subDays(2)])->save();

        $this->artisan('selling-reports:prune')->assertSuccessful();

        $this->assertDatabaseMissing('selling_reports', ['id' => $old->id]);
        $this->assertDatabaseHas('selling_reports', ['id' => $recent->id]);
        Storage::disk('local')->assertMissing('reports/old-selling.xlsx');
        Storage::disk('local')->assertMissing('reports/old-selling.pdf');
        Storage::disk('local')->assertExists('reports/recent-selling.xlsx');
        Storage::disk('local')->assertExists('reports/recent-selling.pdf');
    }

    private function createNotification(User $user, $createdAt): string
    {
        $id = (string) \Illuminate\Support\Str::uuid();
        \Illuminate\Support\Facades\DB::table('notifications')->insert([
            'id' => $id,
            'type' => 'test-notification',
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
            'data' => json_encode(['message' => 'Test notification']),
            'read_at' => null,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);

        return $id;
    }

    private function makeUser(string $role): User
    {
        return User::query()->create([
            'first_name' => 'Report', 'last_name' => ucfirst($role),
            'email' => fake()->unique()->safeEmail(), 'password' => Hash::make('password'),
            'role' => $role, 'status' => 'active',
        ]);
    }
}
