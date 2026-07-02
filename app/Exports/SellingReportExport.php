<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SellingReportExport implements FromCollection, WithHeadings
{
    public function __construct(protected Collection $rows)
    {
        //
    }

    public function collection(): Collection
    {
        return $this->rows->map(function ($row) {
            return [
                'wrapper_name' => $row->wrapper_name,
                'product_name' => $row->product_name,
                'total_quantity' => $row->total_quantity,
                'number_of_orders' => $row->number_of_orders,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Wrapper',
            'Nom du produit',
            'Quantité totale',
            'Nombre de commandes',
        ];
    }
}
