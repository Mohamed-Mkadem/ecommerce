<?php

namespace App\Imports;

use App\Models\Order;
use App\Models\Invoice;
use App\Models\Shipper;
use Maatwebsite\Excel\Row;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Validators\Failure;

class OrderUpdatesImport implements WithHeadingRow, WithValidation, SkipsEmptyRows, OnEachRow, WithChunkReading, SkipsOnFailure
{
    use SkipsFailures;

    public function rules(): array
    {
        return [
            '*.id' => [
                'required',
                'exists:orders,id',
            ],
            '*.statut' => 'required|in:canceled,delivered,returned,shipped',
        ];
    }

    public function onRow(Row $row)
    {
        $rowIndex = $row->getIndex();
        $rowData = $row->toArray();

        $orderId = $rowData['id'];
        $status = $rowData['statut'];


        $order = Order::withTrashed()->find($orderId);

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


        if ($order->trashed()) {
            $this->onFailure(new Failure(
                $rowIndex,
                'id',
                [$messages[app()->getLocale()]['order.isSoftDeleted']],
                $rowData
            ));
            return;
        }


        if ($order->status !== 'shipped' && $order->status !== 'confirmed') {
            $this->onFailure(new Failure(
                $rowIndex,
                'statut',
                [str_replace(':id', $orderId, $messages[app()->getLocale()]['status.not.shipped'])],
                $rowData
            ));
            return;
        }

        if ($order->status === 'shipped' && $status === 'shipped') {
            $this->onFailure(new Failure(
                $rowIndex,
                'statut',
                [str_replace(':id', $orderId, $messages[app()->getLocale()]['status.isAlready.shipped'])],
                $rowData
            ));
            return;
        }


        if (is_null($order->shipper_id)) {
            $this->onFailure(new Failure(
                $rowIndex,
                'shipper_id',
                [str_replace(':id', $orderId, $messages[app()->getLocale()]['order.shipper_null'])],
                $rowData
            ));
            return;
        }

        if (!Shipper::find($order->shipper_id)) {
            $this->onFailure(new Failure(
                $rowIndex,
                'shipper_id',
                [str_replace(':id', $orderId, $messages[app()->getLocale()]['order.shipper.notFound'])],
                $rowData
            ));
            return;
        }

        $order->status = $status;
        $order->save();
    }

    public function customValidationMessages(): array
    {
        $messages = [
            'en' => [
                '*.id.required' => 'The order ID is required for row :row.',
                '*.id.exists' => 'The order ID :input does not exist in the system for row :row.',
                '*.statut.required' => 'The status is required for row :row.',
                '*.statut.in' => 'The status ":input" for row :row must be one of: canceled, delivered, returned, shipped.',
            ],
            'fr' => [
                '*.id.required' => "L'ID de la commande est requis pour la ligne :row.",
                '*.id.exists' => "L'ID de la commande :input n'existe pas dans le système pour la ligne :row.",
                '*.statut.required' => "Le statut est requis pour la ligne :row.",
                '*.statut.in' => "Le statut \":input\" pour la ligne :row doit être l'un des suivants : annulé, livré, retourné, Expédiée.",
            ],
            'ar' => [
                '*.id.required' => 'معرف الطلب مطلوب للصف :row.',
                '*.id.exists' => 'معرف الطلب :input غير موجود في النظام للصف :row.',
                '*.statut.required' => 'الحالة مطلوبة للصف :row.',
                '*.statut.in' => 'يجب أن تكون الحالة ":input" للصف :row واحدة من: تمّ الإلغاء، تم التسليم، تمّ الشحن، تمّ الإرجاع.',
            ]
        ];
        return $messages[app()->getLocale()];
    }

    protected function getCustomMessagesForLogicChecks(): array
    {
        return [
            'en' => [
                'order.notFound' => "The order with ID :id could not be found in the system.", // New message
                'order.isSoftDeleted' => "The order with ID :id is soft-deleted and cannot be updated.", // New message
                'status.not.shipped' => "The order with ID :id cannot be updated because its current status is not 'shipped' or 'confirmed'.",
                'order.shipper_null' => "The order with ID :id cannot be updated because it is not assigned to a shipper.",
                'order.shipper.notFound' => "The order with ID :id cannot be updated because the assigned shipper does not exist.",
                'status.isAlready.shipped' => "The Status of the order with ID :id is already 'Shipped'.",
            ],
            'ar' => [
                'order.notFound' => "لم يتم العثور على الطلب رقم :id في النظام.", // New message
                'order.isSoftDeleted' => "الطلب رقم :id محذوف ولا يمكن تحديثه.", // New message
                'status.not.shipped' => "لا يمكن تحديث الطلب رقم :id لأن حالته ليست 'تمّ التأكيد' أو 'تمّ الشحن'",
                'order.shipper_null' => "لا يمكن تحديث الطلب رقم :id لأنه غير مخصص إلى شاحن.",
                'order.shipper.notFound' => "لا يمكن تحديث الطلب رقم :id لأن الشاحن المعين غير موجود.",
                'status.isAlready.shipped' => "حالة الطلب رقم :id هي بالفعل 'تمّ الشحن'.",
            ],
            'fr' => [
                'order.notFound' => "La commande avec l'ID :id n'a pas pu être trouvée dans le système.", // New message
                'order.isSoftDeleted' => "La commande avec l'ID :id est supprimée (soft-deleted) et ne peut pas être mise à jour.", // New message
                'status.not.shipped' => "La commande avec l'ID :id ne peut pas être mise à jour car son statut actuel n'est pas 'expédié' ou 'confirmée'.",
                'order.shipper_null' => "La commande avec l'ID :id ne peut pas être mise à jour car elle n'est pas attribuée à un transporteur.",
                'order.shipper.notFound' => "La commande avec l'ID :id ne peut pas être mise à jour car le transporteur assigné n'existe pas.",
                'status.isAlready.shipped' => "Le statut de la commande avec L'ID :id est déjà 'Expédié'.",
            ],
        ];
    }



    public function chunkSize(): int
    {
        return 20;
    }
}
