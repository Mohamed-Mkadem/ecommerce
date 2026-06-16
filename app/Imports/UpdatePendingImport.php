<?php

namespace App\Imports;

use App\Models\Order;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Validators\Failure;

class UpdatePendingImport implements WithValidation, WithHeadingRow, WithChunkReading, SkipsEmptyRows, OnEachRow
{
    use SkipsFailures;

    public function rules(): array
    {
        return [
            '*.id' => [
                'required',
                'exists:orders,id',
            ],
        ];
    }

    public function onRow(Row $row)
    {
        $rowIndex = $row->getIndex();
        $rowData = $row->toArray();

        $orderId = $rowData['id'];

        $order = Order::find($orderId);

        $messages = $this->getCustomMessagesForLogicChecks();

        if (!$order) {
            $message = $messages[app()->getLocale()]['order.notFound'] ?? $messages['en']['order.notFound'];
            $message = str_replace(':id', $orderId, $message);

            $this->onFailure(new Failure(
                $rowIndex,
                'id',
                [$message],
                $rowData
            ));

            return;
        }

        // Update status to pending
        $order->update([
            'status' => 'pending',
        ]);
    }

    public function chunkSize(): int
    {
        return 20;
    }

    public function customValidationMessages(): array
    {
        $messages = [
            'en' => [
                '*.id.required' => 'The order ID is required.',
                '*.id.exists' => 'The order ID :input does not exist in the system.',
            ],
            'fr' => [
                '*.id.required' => "L'ID de la commande est requis.",
                '*.id.exists' => "L'ID de la commande :input n'existe pas dans le système.",
            ],
            'ar' => [
                '*.id.required' => 'معرف الطلب مطلوب.',
                '*.id.exists' => 'معرف الطلب :input غير موجود في النظام.',
            ]
        ];

        return $messages[app()->getLocale()];
    }

    protected function getCustomMessagesForLogicChecks(): array
    {
        return [
            'en' => [
                'order.notFound' => "The order with ID :id could not be found in the system.",
            ],
            'ar' => [
                'order.notFound' => "لم يتم العثور على الطلب رقم :id في النظام.",
            ],
            'fr' => [
                'order.notFound' => "La commande avec l'ID :id n'a pas pu être trouvée dans le système.",
            ],
        ];
    }
}
