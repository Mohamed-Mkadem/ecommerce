<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OrderReportExport implements FromCollection, WithHeadings
{
    public function __construct(private readonly Collection $rows)
    {
    }

    public function collection(): Collection
    {
        return $this->rows->map(fn ($row) => [
            'date' => $row['date'],
            'total_order' => $row['total_order'],
            'admin_orders' => $row['admin_orders'],
            'storefront_orders' => $row['storefront_orders'],
        ]);
    }

    public function headings(): array
    {
        return ['Date', 'Total Order', 'Admin Orders', 'Storefront Orders'];
    }
}
