<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleAnalyticsService
{
    private string $measurementId;
    private string $apiSecret;
    private string $baseUrl = 'https://www.google-analytics.com/mp/collect';

    public function __construct()
    {
        $this->measurementId = config('services.google_analytics.measurement_id');
        $this->apiSecret = config('services.google_analytics.api_secret');
    }

    /**
     * Send an event to GA4 via Measurement Protocol
     */
    public function sendEvent(array $payload, bool $debugMode = false): bool
    {
        if (!$this->isConfigured()) {
            return false;
        }

        $url = $this->baseUrl . '?measurement_id=' . $this->measurementId . '&api_secret=' . $this->apiSecret;

        // If debug mode is enabled, add it to the payload to show in DebugView
        // Note: For strict validation we could use /debug/mp/collect but that doesn't show in reports.
        // Adding the 'debug_mode' parameter to the event params makes it show in DebugView.
        if ($debugMode) {
             // We'll handle this at the event level construction usually, but strictly speaking
             // the debug_mode param works when inside the 'params' of an event.
        }

        try {
            Log::info('GA4 Measurement Protocol - Sending Payload', ['payload' => $payload]);

            $response = Http::timeout(30)->post($url, $payload);

            if ($response->successful()) {
                Log::info('GA4 event sent successfully');
                return true;
            } else {
                Log::error('GA4 event failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('GA4 exception', ['message' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Send Purchase Event
     *
     * @param array $orderData
     * @param string $clientId The _ga cookie value (required)
     * @param string|null $sessionId The _ga_<id> cookie value (optional, keeps session continuity)
     */
    public function sendPurchaseEvent(array $orderData, string $clientId, ?string $sessionId = null, bool $debugMode = false): bool
    {
        $params = [
            'currency' => 'TND',
            'transaction_id' => (string) $orderData['order_id'],
            'value' => $orderData['total'],
            'items' => array_map(function ($item) {
                return [
                    'item_id' => (string) $item['id'],
                    'item_name' => $item['name'] ?? 'Product ' . $item['id'], // Fallback if name not available in cart data
                    'quantity' => $item['quantity'],
            
                ];
            }, $orderData['items']),
        ];

        if ($debugMode) {
            $params['debug_mode'] = 1;
        }

        // Engage session if session_id is present
        if ($sessionId) {
            $params['session_id'] = $sessionId;
            $params['engagement_time_msec'] = 100; // Small amount to ensure session logic works
        }

        $payload = [
            'client_id' => $clientId,
            'events' => [
                [
                    'name' => 'purchase',
                    'params' => $params
                ]
            ]
        ];

        return $this->sendEvent($payload, $debugMode);
    }

    public function isConfigured(): bool
    {
        return !empty($this->measurementId) && !empty($this->apiSecret);
    }
}
