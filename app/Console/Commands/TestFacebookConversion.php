<?php

namespace App\Console\Commands;

use App\Services\FacebookConversionsApiService;
use Illuminate\Console\Command;

class TestFacebookConversion extends Command
{
    protected $signature = 'facebook:test-conversion {--event=AddToCart}';
    protected $description = 'Test Facebook Conversion API integration';

    public function handle(FacebookConversionsApiService $facebookService)
    {
        if (!$facebookService->isConfigured()) {
            $this->error('Facebook Conversion API is not properly configured.');
            $this->info('Please check your FACEBOOK_ACCESS_TOKEN and FACEBOOK_PIXEL_ID environment variables.');
            return 1;
        }

        $eventType = $this->option('event');

        $this->info("Testing {$eventType} event...");

        switch ($eventType) {
            case 'AddToCart':
                $success = $facebookService->sendAddToCartEvent([
                    'product_id' => 'test-product-123',
                    'product_name' => 'Test Product',
                    'value' => 25.50,
                    'source_url' => 'https://example.com/test'
                ], [
                    'email' => 'test@example.com',
                    'first_name' => 'Test',
                    'last_name' => 'User'
                ]);
                break;

            case 'InitiateCheckout':
                $success = $facebookService->sendInitiateCheckoutEvent([
                    'total' => 75.00,
                    'product_ids' => ['test-product-123', 'test-product-456'],
                    'source_url' => 'https://example.com/checkout'
                ], [
                    'email' => 'test@example.com',
                    'first_name' => 'Test',
                    'last_name' => 'User'
                ]);
                break;

            case 'Purchase':
                $success = $facebookService->sendPurchaseEvent([
                    'order_id' => 'test-order-789',
                    'total' => 100.00,
                    'product_ids' => ['test-product-123', 'test-product-456'],
                    'source_url' => 'https://example.com/order-complete'
                ], [
                    'email' => 'test@example.com',
                    'first_name' => 'Test',
                    'last_name' => 'User'
                ]);
                break;

            default:
                $this->error("Unknown event type: {$eventType}");
                $this->info('Available events: AddToCart, InitiateCheckout, Purchase');
                return 1;
        }

        if ($success) {
            $this->info("✅ {$eventType} event sent successfully!");
            $this->info('Check your Facebook Events Manager to verify the event was received.');
        } else {
            $this->error("❌ Failed to send {$eventType} event.");
            $this->info('Check your logs for more details.');
        }

        return 0;
    }
}
