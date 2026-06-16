<?php

namespace App\Jobs;

use App\Notifications\SendingOrderToShippingCompanyNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Events\SendingOrderToShippingCompanyFailed;

class SendOrderToNavex implements ShouldQueue
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

        $token = config('services.navex.token');
        $url = config('services.navex.url');

        try {
            $response = \Illuminate\Support\Facades\Http::timeout(30)
                ->asForm()
                ->withBasicAuth($token, '')
                ->post($url, $data);

            $responseData = $response->json();

            if (!isset($responseData['status']) || $responseData['status'] != 1) {
                Log::error('Navex API error: ' . $response->body());
                event(new SendingOrderToShippingCompanyFailed($this->order));
            }
        } catch (\Exception $e) {
            event(new SendingOrderToShippingCompanyFailed($this->order));
            Log::error('Navex API exception: ' . $e->getMessage());
        }
    }


    private function getData()
    {
        return [
            'prix'         => $this->order->amount / 1000,
            'nom'          => "{$this->order->client_name} (#{$this->order->id})",
            'gouvernerat'  => $this->order->state->translate('fr')->name,
            'ville'        => $this->order->city->translate('fr')->name,
            'adresse'      => $this->getFullAddress(),
            'tel'          => $this->order->phone,
            'tel2'         => $this->order->phone2,
            'designation'  => $this->getDesignation(),
            'nb_article'   =>  1,
            'msg'          => $this->getMsg(),
            'echange'      => "Non",
            'article'      => "",
            'nb_echange'   => "",
            'ouvrir'       => 'Oui',
        ];
    }

    private function getFullAddress(): string
    {
        return $this->order->address . " " .  $this->order->locality->translate('fr')->name;
    }
    private function getDesignation()
    {
        return $this->order->products->map(function ($product) {
            return  $product->pivot->quantity . " "  . $product->shipping_name;
        })->implode(' / ');
    }

    private function getMsg()
    {
        $msg = "Trés Fragile \n";
        $msg .= " / Numéro de commande: " . $this->order->id . "\n";

        $deliveryDate = Carbon::parse($this->order->delivery_date);
        $today = Carbon::now();

        $expectedDeliveryDay = null;

        if ($today->isSaturday()) {
            $expectedDeliveryDay = Carbon::parse('next monday');
        } else {
            $expectedDeliveryDay = Carbon::tomorrow();
        }


        if (!$deliveryDate->isSameDay($expectedDeliveryDay)) {
            $msg .= " / Date de Livraison : " . $deliveryDate->format('d/m/Y') . "\n";
        }

        return $msg;
    }
}
