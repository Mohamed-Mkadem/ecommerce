<?php

namespace App\Jobs;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Events\SendingOrderToShippingCompanyFailed;

class SendOrderToOnesta implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public $order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $data = $this->getData();

        $user = config('services.onesta.user');
        $pass = config('services.onesta.pass');
        $url = config('services.onesta.url');

        $data['Utilisateur'] = $user;
        $data['Pass'] = $pass;

        try {
            $response = Http::timeout(30)
                ->acceptJson()
                ->post($url, $data);

            $responseData = $response->json();

            if (
                !isset($responseData['result_type']) ||
                $responseData['result_type'] !== 'success'
            ) {
                Log::error('Onesta API error: ' . $response->body());
                event(new SendingOrderToShippingCompanyFailed($this->order));
            }
        } catch (\Exception $e) {
            event(new SendingOrderToShippingCompanyFailed($this->order));
            Log::error('Onesta API exception: ' . $e->getMessage());
        }
    }
    private function getData(): array
    {
        return [
            "reference"    => $this->order->id,
            "client"       => "{$this->order->client_name} (#{$this->order->id})",
            "adresse"      => $this->getFullAddress(),
            "code_postal"  => $this->order->locality->postal_code ?? '',
            "nb_pieces"    => 1,
            "prix"         => $this->order->amount / 1000,
            "tel1"         => $this->order->phone,
            "tel2"         => $this->order->phone2 ?? '',
            "designation"  => $this->getDesignation(),
            "commentaire"  => $this->getMsg(),
            "type"         => "FIX",
            "echange"      => 0,
        ];
    }

    private function getFullAddress(): string
    {
        return $this->order->address . " " . ($this->order->locality->translate('fr')->name ?? '');
    }

    private function getDesignation()
    {
        return $this->order->products->map(function ($product) {
            return "x" . $product->pivot->quantity . " " .  $product->shipping_name;
        })->implode(' / ');
    }
    private function getMsg()
    {
        $msg = "Trés Fragile \n";
        $msg .= "Numéro de commande: " . $this->order->id . "\n";

        $deliveryDate = Carbon::parse($this->order->delivery_date);
        $today = Carbon::now();

        $expectedDeliveryDay = null;

        if ($today->isSaturday()) {
            $expectedDeliveryDay = Carbon::parse('next monday');
        } else {
            $expectedDeliveryDay = Carbon::tomorrow();
        }


        if (!$deliveryDate->isSameDay($expectedDeliveryDay)) {
            $msg .= "Date de Livraison : " . $deliveryDate->format('d/m/Y') . "\n";
        }

        return $msg;
    }
}
