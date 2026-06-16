<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class ShippingReportExport implements FromCollection,  WithHeadings, WithMapping
{
    protected $orders;

    public function __construct($orders, public $grandTunisShipper = false)
    {
        $this->orders = $orders;
        $this->grandTunisShipper = $grandTunisShipper;
    }

    public function collection()
    {
        return $this->orders;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Statut',
            'Client',
            'Téléphone',
            'Adresse',
            'Désignation',
            'Montant',
            $this->grandTunisShipper ? 'Livreur' : ''
        ];
    }

    public function map($order): array
    {
        return [
            $order->id,
            'shipped',
            $order->client_name,
            $order->phone2 ? "$order->phone / $order->phone2" : $order->phone,
            $this->getFullAddress($order),
            $order->products->map(function ($product) {
                return  $product->pivot->quantity . " "  . $product->shipping_name;
            })->implode(' + '),
            $order->amount / 1000,
            $this->grandTunisShipper ?  $order->shipper->id : '',
        ];
    }

    private function getFullAddress($order): string
    {

        return $order->address . " " . $order->locality->translate('fr')->name . " " . $order->city->translate('fr')->name . " " . $order->state->translate('fr')->name;
    }
}
