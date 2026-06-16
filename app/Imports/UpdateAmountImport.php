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

class UpdateAmountImport implements WithValidation, WithHeadingRow, WithChunkReading, SkipsEmptyRows, OnEachRow
{
    use SkipsFailures;

    public function rules(): array
    {
        return [
            '*.id' => [
                'required',
                'exists:orders,id',
            ],
            '*.montant' => [
                'required',
                'numeric',
                'min:0',
            ],
        ];
    }

    public function onRow(Row $row)
    {
        $rowIndex = $row->getIndex();
        $rowData = $row->toArray();

        $orderId = $rowData['id'];
        $amountInDt = $rowData['montant'];

        $order = Order::find($orderId);

        $messages = $this->getCustomMessagesForLogicChecks();

        if (!$order) {
            $this->onFailure(new Failure(
                $rowIndex,
                'id',
                [$messages[app()->getLocale()]['order.notFound']],
                $rowData
            ));
            return;
        }

        // Convert amount from DT to millimes (multiply by 1000)
        $amountInMillimes = (int)($amountInDt * 1000);

        $order->update([
            'amount' => $amountInMillimes,
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
                '*.amount.required' => 'The amount is required.',
                '*.amount.numeric' => 'The amount must be a number.',
                '*.amount.min' => 'The amount must be greater than or equal to 0.',
            ],
            'fr' => [
                '*.id.required' => "L'ID de la commande est requis.",
                '*.id.exists' => "L'ID de la commande :input n'existe pas dans le système.",
                '*.amount.required' => "Le montant est requis.",
                '*.amount.numeric' => "Le montant doit être un nombre.",
                '*.amount.min' => "Le montant doit être supérieur ou égal à 0.",
            ],
            'ar' => [
                '*.id.required' => 'معرف الطلب مطلوب.',
                '*.id.exists' => 'معرف الطلب :input غير موجود في النظام.',
                '*.amount.required' => 'المبلغ مطلوب.',
                '*.amount.numeric' => 'يجب أن يكون المبلغ رقماً.',
                '*.amount.min' => 'يجب أن يكون المبلغ أكبر من أو يساوي 0.',
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
