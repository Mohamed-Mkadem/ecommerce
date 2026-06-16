<?php

namespace App\Jobs;

use App\Services\FacebookConversionsApiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendFacebookConversionEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 30;

    private string $eventType;
    private array $eventData;
    private array $userData;

    /**
     * Create a new job instance.
     */
    public function __construct(string $eventType, array $eventData, array $userData = [])
    {
        $this->eventType = $eventType;
        $this->eventData = $eventData;
        $this->userData = $userData;
    }

    /**
     * Execute the job.
     */
    public function handle(FacebookConversionsApiService $facebookService): void
    {
        try {
            if (!$facebookService->isConfigured()) {
                Log::warning('Facebook Conversions API not configured, skipping event', [
                    'event_type' => $this->eventType
                ]);
                return;
            }

            // We only send Purchase via the Conversions API now.
            $success = match ($this->eventType) {
                'Purchase' => $facebookService->sendPurchaseEvent($this->eventData, $this->userData),
                default => false,
            };

            if (!$success) {
                throw new \Exception("Failed to send {$this->eventType} event to Facebook Conversions API");
            }

            Log::info("Facebook Conversions API {$this->eventType} event sent successfully", [
                'event_data' => $this->eventData
            ]);
        } catch (\Exception $e) {
            Log::error("Facebook Conversions API job failed: {$e->getMessage()}", [
                'event_type' => $this->eventType,
                'event_data' => $this->eventData
            ]);

            // Re-throw to trigger retry mechanism
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Facebook Conversions API job permanently failed after {$this->tries} attempts", [
            'event_type' => $this->eventType,
            'event_data' => $this->eventData,
            'error' => $exception->getMessage()
        ]);
    }
}
