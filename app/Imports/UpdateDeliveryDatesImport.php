<?php

namespace App\Imports;

use App\Models\Order;
use Maatwebsite\Excel\Row;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class UpdateDeliveryDatesImport implements WithValidation, WithHeadingRow, WithChunkReading, SkipsEmptyRows, OnEachRow
{


    use SkipsFailures;

    protected $errors = [];
    public function __construct(public $delliveryDate)
    {
        $this->delliveryDate = $delliveryDate;
    }

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
        $row = $row->toArray();
        $orderID = $row['id'];

        $order = Order::find($orderID);
        if ($order) {
            $order->update([
                'delivery_date' => $this->delliveryDate,
            ]);

             if (!empty($this->errors)) {
                throw ValidationException::withMessages($this->errors);
            }
        } 

     }

    public function chunkSize(): int
    {
        return 20;
    }

       public function customValidationMessages()
    {
        $messages = [
            'en' => [
                'id.required' => 'The order ID is required.',
                'id.exists' => 'The order ID does not exist in the system.',
               

            ],
            'fr' => [
                'id.required' => "L'ID de la commande est requis.",
                'id.exists' => "L'ID de la commande n'existe pas dans le système.",
                

            ],
            'ar' => [
                'id.required' => 'معرف الطلب مطلوب.',
                'id.exists' => 'معرف الطلب غير موجود في النظام.',
               

            ]
        ];


        return $messages[app()->getLocale()];
    }
}
