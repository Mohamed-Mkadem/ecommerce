<?php

namespace Tests\Feature\FrontEnd;

use App\Models\CouponCode;
use App\Models\Order;
use App\Models\Product;
use App\Models\ShippingSetting;
use App\Models\State;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class CheckoutPricingTest extends TestCase
{
    use RefreshDatabase;

    private State $state;

    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();

        $this->state = State::factory()->create([
            'shipping_cost' => 8000,
            'en' => ['name' => 'Tunis'],
            'fr' => ['name' => 'Tunis'],
            'ar' => ['name' => 'تونس'],
        ]);

        ShippingSetting::create([
            'tunis_acceptance_delivery_date' => '2026-10-02',
            'wilayet_acceptance_delivery_date' => '2026-10-03',
        ]);
    }

    public function test_order_total_includes_shipping_fee_when_no_coupon_or_free_shipping_applies(): void
    {
        $product = Product::factory()->create(['price' => 100000]);

        $response = $this->post(route('FE.orders.place'), $this->orderPayload($product, 108));

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'phone' => '23456789',
            'amount' => 108000,
            'shipping_cost' => 8000,
            'free_shipping' => false,
            'status' => 'pending',
        ]);
    }

    public function test_active_coupon_discount_is_applied_before_shipping_is_added(): void
    {
        $product = Product::factory()->create(['price' => 100000]);
        $coupon = CouponCode::create([
            'code' => 'TENOFF',
            'status' => 'active',
            'value' => 10,
        ]);

        $response = $this->post(route('FE.orders.place'), $this->orderPayload(
            product: $product,
            total: 98,
            coupon: ['id' => $coupon->id, 'value' => 10, 'code' => $coupon->code],
        ));

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'phone' => '23456789',
            'amount' => 98000,
            'shipping_cost' => 8000,
            'coupon_code_id' => $coupon->id,
        ]);
    }

    public function test_checkout_rejects_an_inactive_coupon_even_if_its_id_and_discount_are_submitted(): void
    {
        $product = Product::factory()->create(['price' => 100000]);
        $coupon = CouponCode::create(['code' => 'OLD10', 'status' => 'inactive', 'value' => 10]);
        $payload = $this->orderPayload($product, 98, [
            'id' => $coupon->id,
            'value' => 10,
            'code' => $coupon->code,
        ]);

        $this->postJson(route('FE.orders.place'), $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors('coupon.id');
    }

    public function test_checkout_calculates_shipping_from_the_selected_state_not_the_submitted_fee(): void
    {
        $product = Product::factory()->create(['price' => 100000]);
        $payload = $this->orderPayload($product, 101);
        $payload['state']['shipping_cost'] = 1;

        $this->postJson(route('FE.orders.place'), $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors('total');
    }

    public function test_free_shipping_is_not_added_even_when_an_active_coupon_is_applied(): void
    {
        $product = Product::factory()->create(['price' => 100000]);
        $coupon = CouponCode::create([
            'code' => 'TENOFF',
            'status' => 'active',
            'value' => 10,
        ]);

        $payload = $this->orderPayload(
            product: $product,
            total: 90,
            coupon: ['id' => $coupon->id, 'value' => 10, 'code' => $coupon->code],
            freeShipping: true,
        );
        $payload['cart'][0]['free_shipping'] = true;

        $response = $this->post(route('FE.orders.place'), $payload);

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'phone' => '23456789',
            'amount' => 90000,
            'shipping_cost' => 0,
            'free_shipping' => true,
            'coupon_code_id' => $coupon->id,
        ]);
    }

    public function test_checkout_rejects_a_total_that_does_not_match_server_calculated_pricing(): void
    {
        $product = Product::factory()->create(['price' => 100000]);

        $response = $this->postJson(route('FE.orders.place'), $this->orderPayload($product, 99));

        $response->assertUnprocessable()->assertJsonValidationErrors('total');
        $this->assertSame(0, Order::count());
    }

    public function test_coupon_lookup_returns_only_active_codes(): void
    {
        $active = CouponCode::create(['code' => 'ACTIVE10', 'status' => 'active', 'value' => 10]);
        CouponCode::create(['code' => 'INACTIVE10', 'status' => 'inactive', 'value' => 10]);

        $this->postJson(route('FE.codes.getCode'), ['code' => 'ACTIVE10'])
            ->assertOk()
            ->assertJsonPath('id', $active->id);

        $this->postJson(route('FE.codes.getCode'), ['code' => 'INACTIVE10'])
            ->assertOk()
            ->assertContent('');
    }

    /** @dataProvider invalidPhoneNumbers */
    public function test_checkout_rejects_phone_numbers_outside_the_tunisian_rules(string $phone): void
    {
        $product = Product::factory()->create(['price' => 100000]);
        $payload = $this->orderPayload($product, 108);
        $payload['phone'] = $phone;

        $this->postJson(route('FE.orders.place'), $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors('phone');
    }

    public static function invalidPhoneNumbers(): array
    {
        return [
            'leading zero' => ['01234567'],
            'leading one' => ['11234567'],
            'leading six' => ['61234567'],
            'leading eight' => ['81234567'],
            'too short' => ['2345678'],
            'too long' => ['234567890'],
        ];
    }

    private function orderPayload(
        Product $product,
        float $total,
        ?array $coupon = null,
        bool $freeShipping = false,
    ): array {
        return [
            'name' => 'Test Customer',
            'address' => '1 Test Street',
            'phone' => '23456789',
            'state' => ['id' => $this->state->id, 'shipping_cost' => 8],
            'coupon' => $coupon,
            'cart' => [[
                'id' => $product->id,
                'quantity' => 1,
                'price' => 100,
                'free_shipping' => $freeShipping,
            ]],
            'total' => $total,
            'free_shipping' => $freeShipping,
        ];
    }
}
