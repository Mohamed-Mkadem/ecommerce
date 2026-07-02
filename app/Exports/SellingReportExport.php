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
        return $this->rows;
    }

    public function headings(): array
    {
        return [
            'Nom du produit',
            'Quantité totale',
            'Nombre de commandes',
        ];
    }
}
