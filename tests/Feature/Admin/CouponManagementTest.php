<?php

namespace Tests\Feature\Admin;

use App\Models\Client;
use App\Models\CouponCode;
use App\Models\Order;
use App\Models\State;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CouponManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_a_percentage_coupon_and_duplicate_or_out_of_range_values_are_rejected(): void
    {
        $admin = $this->makeUser('admin');

        $this->actingAs($admin)->post(route('coupons.store'), [
            'code' => 'SWEET10',
            'value' => 10,
            'status' => 'active',
        ])->assertRedirect(route('coupons.index'));

        $this->assertDatabaseHas('coupon_codes', [
            'code' => 'SWEET10',
            'value' => 10,
            'status' => 'active',
        ]);

        $invalid = [
            'code' => 'SWEET10',
            'value' => 101,
            'status' => 'paused',
        ];
        $this->actingAs($admin)->postJson(route('coupons.store'), $invalid)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['code', 'value', 'status']);

        $invalid['code'] = 'ZERO';
        $invalid['value'] = 0;
        $invalid['status'] = 'inactive';
        $this->actingAs($admin)->postJson(route('coupons.store'), $invalid)
            ->assertUnprocessable()
            ->assertJsonValidationErrors('value');

        $this->assertSame(1, CouponCode::query()->count());
    }

    public function test_admin_can_activate_and_deactivate_a_coupon_and_storefront_lookup_only_returns_active_codes(): void
    {
        $admin = $this->makeUser('admin');
        $coupon = CouponCode::query()->create([
            'code' => 'SWEET15',
            'value' => 15,
            'status' => 'inactive',
        ]);

        $this->actingAs($admin)->patch(route('coupons.update', $coupon), ['status' => 'active'])
            ->assertRedirect(route('coupons.index'));
        $this->assertSame('active', $coupon->fresh()->status);
        $this->postJson(route('FE.codes.getCode'), ['code' => 'SWEET15'])
            ->assertOk()
            ->assertJsonPath('id', $coupon->id)
            ->assertJsonPath('value', 15);

        $this->actingAs($admin)->patch(route('coupons.update', $coupon), ['status' => 'inactive'])
            ->assertRedirect(route('coupons.index'));
        $this->assertSame('inactive', $coupon->fresh()->status);
        $this->postJson(route('FE.codes.getCode'), ['code' => 'SWEET15'])
            ->assertOk()
            ->assertContent('');
    }

    public function test_deleting_a_coupon_with_order_history_soft_deletes_it_and_unused_coupon_is_removed(): void
    {
        $admin = $this->makeUser('admin');
        $couponWithOrder = CouponCode::query()->create([
            'code' => 'USED10',
            'value' => 10,
            'status' => 'active',
        ]);
        $this->makeOrderUsingCoupon($couponWithOrder);
        $unusedCoupon = CouponCode::query()->create([
            'code' => 'UNUSED10',
            'value' => 10,
            'status' => 'inactive',
        ]);

        $this->actingAs($admin)->delete(route('coupons.destroy', $couponWithOrder))
            ->assertRedirect(route('coupons.index'));
        $this->assertSoftDeleted('coupon_codes', ['id' => $couponWithOrder->id]);

        $this->actingAs($admin)->delete(route('coupons.destroy', $unusedCoupon))
            ->assertRedirect(route('coupons.index'));
        $this->assertDatabaseMissing('coupon_codes', ['id' => $unusedCoupon->id]);
    }

    public function test_coupon_management_routes_are_admin_only(): void
    {
        $moderator = $this->makeUser('moderator');
        $coupon = CouponCode::query()->create([
            'code' => 'ADMINONLY',
            'value' => 10,
            'status' => 'active',
        ]);

        $this->actingAs($moderator)->get(route('coupons.index'))->assertForbidden();
        $this->actingAs($moderator)->get(route('coupons.create'))->assertForbidden();
        $this->actingAs($moderator)->postJson(route('coupons.store'), [
            'code' => 'NOACCESS',
            'value' => 10,
            'status' => 'active',
        ])->assertForbidden();
        $this->actingAs($moderator)->patchJson(route('coupons.update', $coupon), ['status' => 'inactive'])
            ->assertForbidden();
        $this->actingAs($moderator)->deleteJson(route('coupons.destroy', $coupon))->assertForbidden();
    }

    private function makeOrderUsingCoupon(CouponCode $coupon): Order
    {
        $state = State::factory()->create();
        $client = Client::query()->create([
            'name' => 'Coupon Customer',
            'phone' => '22123456',
            'address' => 'Tunis',
            'state_id' => $state->id,
        ]);

        return Order::query()->create([
            'client_id' => $client->id,
            'state_id' => $state->id,
            'coupon_code_id' => $coupon->id,
            'status' => 'pending',
            'source' => 'admin',
            'amount' => 35000,
            'shipping_cost' => 5000,
            'client_name' => $client->name,
            'address' => $client->address,
            'phone' => $client->phone,
            'delivery_date' => now()->addDay()->toDateString(),
        ]);
    }

    private function makeUser(string $role): User
    {
        return User::query()->create([
            'first_name' => 'Coupon',
            'last_name' => ucfirst($role),
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'role' => $role,
            'status' => 'active',
        ]);
    }
}
