<?php

namespace App\Jobs;

use App\Services\GoogleAnalyticsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendGoogleAnalyticsPurchaseEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 30;

    private array $orderData;
    private string $clientId;
    private ?string $sessionId;
    private bool $debugMode;

    /**
     * Create a new job instance.
     */
    public function __construct(array $orderData, string $clientId, ?string $sessionId = null, bool $debugMode = false)
    {
        $this->orderData = $orderData;
        $this->clientId = $clientId;
        $this->sessionId = $sessionId;
        $this->debugMode = $debugMode;
    }

    /**
     * Execute the job.
     */
    public function handle(GoogleAnalyticsService $gaService): void
    {
        try {
            if (!$gaService->isConfigured()) {
                Log::warning('GA4 Service not configured, skipping event');
                return;
            }

            $success = $gaService->sendPurchaseEvent(
                $this->orderData,
                $this->clientId,
                $this->sessionId,
                $this->debugMode
            );

            if (!$success) {
                throw new \Exception("Failed to send GA4 Purchase event");
            }

        } catch (\Exception $e) {
            Log::error("GA4 Job Failed: {$e->getMessage()}", [
                'order_id' => $this->orderData['order_id'] ?? 'unknown'
            ]);
            throw $e;
        }
    }
}
