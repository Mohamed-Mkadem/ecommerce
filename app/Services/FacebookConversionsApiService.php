<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class FacebookConversionsApiService
{
    private string $accessToken;
    private string $pixelId;
    private string $apiVersion;
    private string $baseUrl;

    public function __construct()
    {
        $this->accessToken = config('services.facebook.access_token');
        $this->pixelId = config('services.facebook.pixel_id');
        $this->apiVersion = config('services.facebook.api_version', 'v18.0');
        $this->baseUrl = "https://graph.facebook.com/{$this->apiVersion}";
    }

    /**
     * Send a conversion event to Facebook Conversions API
     */
    public function sendEvent(array $eventData): bool
    {
        try {
            $payload = ['data' => [$eventData]];

            // Only add test_event_code if it's configured (for testing)
            if (config('services.facebook.test_event_code')) {
                $payload['test_event_code'] = config('services.facebook.test_event_code');
            }

            // Log the payload before sending to Facebook Conversions API
            Log::info('Facebook Conversions API - Payload before sending', [
                'event_name' => $eventData['event_name'] ?? 'unknown',
                'event_id' => $eventData['event_id'] ?? null,
                'payload' => $payload,
                'user_data_keys' => array_keys($eventData['user_data'] ?? []),
                'user_data' => $eventData['user_data'] ?? [],
                'custom_data' => $eventData['custom_data'] ?? [],
            ]);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/{$this->pixelId}/events", $payload);

            if ($response->successful()) {
                Log::info('Facebook Conversions API event sent successfully', [
                    'event_name' => $eventData['event_name'] ?? 'unknown',
                    'response' => $response->json()
                ]);
                return true;
            } else {
                Log::error('Facebook Conversions API event failed', [
                    'event_name' => $eventData['event_name'] ?? 'unknown',
                    'status' => $response->status(),
                    'response' => $response->json()
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Facebook Conversions API exception', [
                'message' => $e->getMessage(),
                'event_name' => $eventData['event_name'] ?? 'unknown'
            ]);
            return false;
        }
    }

    /**
     * Create a Purchase event for order completion
     */
    public function sendPurchaseEvent(array $orderData, array $userData = []): bool
    {
        $eventData = [
            'event_name' => 'Purchase',
            'event_time' => time(),
            'action_source' => 'website',
            'event_id' => $orderData['order_id'] ?? uniqid(),
            'user_data' => $this->prepareUserData($userData),
            'custom_data' => [
                'currency' => 'TND',
                'value' => $orderData['total'],
                'content_ids' => $orderData['product_ids'] ?? [],
                'content_type' => 'product',
                'order_id' => $orderData['order_id'] ?? null,
            ],
            'event_source_url' => $orderData['source_url'] ?? url()->current(),
        ];

        return $this->sendEvent($eventData);
    }

    /**
     * Prepare user data for Facebook Conversions API
     * Hash user data using SHA-256 as required by Facebook
     */
    private function prepareUserData(array $userData): array
    {
        $preparedData = [];


        // Phone if provided - hash with SHA-256
        if (!empty($userData['phone'])) {
            $phone = preg_replace('/[^0-9]/', '', $userData['phone']);
            $preparedData['ph'] = [hash('sha256', $phone)];
        }

        // First name if provided - hash with SHA-256
        if (!empty($userData['first_name'])) {
            $firstName = strtolower(trim($userData['first_name']));
            $preparedData['fn'] = [hash('sha256', $firstName)];
        }

        // Last name if provided - hash with SHA-256
        if (!empty($userData['last_name'])) {
            $lastName = strtolower(trim($userData['last_name']));
            $preparedData['ln'] = [hash('sha256', $lastName)];
        }


        // State if provided - hash with SHA-256
        if (!empty($userData['state'])) {
            $state = strtolower(trim($userData['state']));
            $preparedData['st'] = [hash('sha256', $state)];
        }

        // Country if provided - hash with SHA-256
        if (!empty($userData['country'])) {
            $country = 'Tunisia';
            $preparedData['country'] = [hash('sha256', $country)];
        }

        // Client IP address (not hashed as per Facebook requirements)
        // Use passed value first (for queued jobs), fallback to request() for direct calls
        if (!empty($userData['client_ip_address'])) {
            $preparedData['client_ip_address'] = $userData['client_ip_address'];
        } else {
            try {
                $ip = request()->ip();
                if (!empty($ip)) {
                    $preparedData['client_ip_address'] = $ip;
                }
            } catch (\Exception $e) {
                // Request context not available (e.g., in queue worker)
            }
        }

        // User agent (not hashed as per Facebook requirements)
        // Use passed value first (for queued jobs), fallback to request() for direct calls
        if (!empty($userData['client_user_agent'])) {
            $preparedData['client_user_agent'] = $userData['client_user_agent'];
        } else {
            try {
                $userAgent = request()->userAgent();
                if (!empty($userAgent)) {
                    $preparedData['client_user_agent'] = $userAgent;
                }
            } catch (\Exception $e) {
                // Request context not available (e.g., in queue worker)
            }
        }



        return $preparedData;
    }

    /**
     * Validate that required configuration is present
     */
    public function isConfigured(): bool
    {
        return !empty($this->accessToken) && !empty($this->pixelId);
    }
}
