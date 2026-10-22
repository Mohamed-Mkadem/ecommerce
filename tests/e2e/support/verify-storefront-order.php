<?php

use Illuminate\Support\Facades\DB;

require dirname(__DIR__, 3) . '/vendor/autoload.php';

$app = require dirname(__DIR__, 3) . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$phone = getenv('E2E_ORDER_PHONE') ?: '';
$marker = getenv('E2E_ORDER_MARKER') ?: '';
$expectedProductId = (int) (getenv('E2E_EXPECTED_PRODUCT_ID') ?: 0);
$expectedQuantity = (float) (getenv('E2E_EXPECTED_QUANTITY') ?: 0);

if (! preg_match('/^2\d{7}$/', $phone) || ! str_starts_with($marker, 'E2E Storefront Order ')) {
    fwrite(STDERR, "Refusing verification without a valid E2E order identity.\n");
    exit(1);
}

$order = DB::table('orders as o')
    ->leftJoin('coupon_codes as c', 'c.id', '=', 'o.coupon_code_id')
    ->where('o.phone', $phone)
    ->where('o.client_name', $marker)
    ->where('o.source', 'client')
    ->latest('o.id')
    ->select('o.id', 'o.client_name', 'o.phone', 'o.status', 'o.source', 'o.amount', 'o.shipping_cost', 'o.coupon_code_id', 'c.code as coupon_code')
    ->first();

if (! $order) {
    fwrite(STDERR, "The expected storefront order was not found.\n");
    exit(1);
}

$item = DB::table('order_product as op')
    ->join('products as p', 'p.id', '=', 'op.product_id')
    ->where('op.order_id', $order->id)
    ->where('op.product_id', $expectedProductId)
    ->select('op.product_id', 'op.quantity', 'p.price')
    ->first();

if (! $item || (float) $item->quantity !== $expectedQuantity || $order->coupon_code !== 'BG10') {
    fwrite(STDERR, "The order is missing the expected coupon, variant, or updated quantity.\n");
    exit(1);
}

$expectedAmount = (int) ($item->price * $item->quantity * 0.9) + (int) $order->shipping_cost;
if ((int) $order->amount !== $expectedAmount) {
    fwrite(STDERR, "The order total does not match the 10% coupon discount and shipping.\n");
    exit(1);
}

echo json_encode([
    'id' => $order->id,
    'client_name' => $order->client_name,
    'phone' => $order->phone,
    'status' => $order->status,
    'source' => $order->source,
    'amount' => $order->amount,
    'shipping_cost' => $order->shipping_cost,
    'coupon_code_id' => $order->coupon_code_id,
    'coupon_code' => $order->coupon_code,
    'product_id' => $item->product_id,
    'quantity' => $item->quantity,
], JSON_THROW_ON_ERROR);
