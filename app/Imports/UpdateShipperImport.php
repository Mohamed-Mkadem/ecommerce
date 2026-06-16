<?php

namespace App\Imports;

use App\Models\Order;
use App\Models\Shipper;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Validators\Failure;

class UpdateShipperImport implements WithValidation, WithHeadingRow, WithChunkReading, SkipsEmptyRows, OnEachRow
{
    use SkipsFailures;

    public function rules(): array
    {
        return [
            '*.id' => [
                'required',
                'exists:orders,id',
            ],
            '*.livreur' => [
                'required',
                'exists:shippers,id',
            ],
        ];
    }

    public function onRow(Row $row)
    {
        $rowIndex = $row->getIndex();
        $rowData = $row->toArray();

        $orderId = $rowData['id'];
        $shipperId = $rowData['livreur'];

        $order = Order::find($orderId);
        $shipper = Shipper::find($shipperId);

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

        if (!$shipper) {
            $this->onFailure(new Failure(
                $rowIndex,
                'shipper_id',
                [str_replace(':id', $shipperId, $messages[app()->getLocale()]['shipper.notFound'])],
                $rowData
            ));
            return;
        }

        $order->update([
            'shipper_id' => $shipperId,
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
                '*.shipper_id.required' => 'The shipper ID is required.',
                '*.shipper_id.exists' => 'The shipper ID :input does not exist in the system.',
            ],
            'fr' => [
                '*.id.required' => "L'ID de la commande est requis.",
                '*.id.exists' => "L'ID de la commande :input n'existe pas dans le système.",
                '*.shipper_id.required' => "L'ID du livreur est requis.",
                '*.shipper_id.exists' => "L'ID du livreur :input n'existe pas dans le système.",
            ],
            'ar' => [
                '*.id.required' => 'معرف الطلب مطلوب.',
                '*.id.exists' => 'معرف الطلب :input غير موجود في النظام.',
                '*.shipper_id.required' => 'معرف الشاحن مطلوب.',
                '*.shipper_id.exists' => 'معرف الشاحن :input غير موجود في النظام.',
            ]
        ];

        return $messages[app()->getLocale()];
    }

    protected function getCustomMessagesForLogicChecks(): array
    {
        return [
            'en' => [
                'order.notFound' => "The order with ID :id could not be found in the system.",
                'shipper.notFound' => "The shipper with ID :id does not exist in the system.",
            ],
            'ar' => [
                'order.notFound' => "لم يتم العثور على الطلب رقم :id في النظام.",
                'shipper.notFound' => "الشاحن رقم :id غير موجود في النظام.",
            ],
            'fr' => [
                'order.notFound' => "La commande avec l'ID :id n'a pas pu être trouvée dans le système.",
                'shipper.notFound' => "Le transporteur avec l'ID :id n'existe pas dans le système.",
            ],
        ];
    }
}
