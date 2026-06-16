<?php

namespace App\Imports;

use App\Models\Client;
use App\Models\Note;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Validators\Failure;

class ExcelOrderImport implements OnEachRow, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    use SkipsFailures;




    /**
     * @param Row $row
     */
    public function onRow(Row $row)
    {
        $row = array_change_key_case($row->toArray(), CASE_LOWER);

        DB::transaction(function () use ($row) {
            $client = Client::firstOrNew([
                'phone' => $row['phone'],
            ]);

            $client->name = filled($row['client_name']) ? $row['client_name'] : ($client->name ?: 'client');
            $client->address = filled($row['address']) ? $row['address'] : ($client->address ?: 'N.D');
            $client->state_id = $row['state_id'] ?: ($client->state_id ?: 1);
            $client->save();

            $pendingAbandonedOrders = Order::with('nrp')
                ->where('client_id', $client->id)
                ->whereIn('status', ['pending', 'abandoned'])
                ->get();

            $ordersWithoutNrp = $pendingAbandonedOrders->filter(fn($existingOrder) => !$existingOrder->nrp)->values();

            if ($ordersWithoutNrp->isNotEmpty()) {
                return;
            }

            $ordersWithNrp = $pendingAbandonedOrders->filter(fn($existingOrder) => $existingOrder->nrp)->values();
            foreach ($ordersWithNrp as $existingOrder) {
                $existingOrder->status = 'canceled';
                $existingOrder->save();
            }

            $order = Order::create([
                'client_id' => $client->id,
                'state_id' => $client->state_id,
                'coupon_code_id' => null,
                'shipper_id' => null,
                'status' => 'pending',
                'source' => 'admin',
                'amount' => 0,
                'shipping_cost' => 0,
                'client_name' => $row['client_name'] ?: 'client',
                'address' => $row['address'] ?: 'N.D',
                'note' => 'Imported from Excel',
                'phone' => $row['phone'],
                'delivery_date' => Carbon::today()->toDateString(),
            ]);



            $defaultProductId = config('services.defult_product_id', 73);
            $defaultProduct = Product::find($defaultProductId);

            if (! $defaultProduct) {
                throw new \Exception('No available product found to attach to imported orders.');
            }

            $price = $defaultProduct->price ?? 0;
            $quantity = 1;
            $subTotal = $price * $quantity;

            OrderProduct::create([
                'order_id' => $order->id,
                'product_id' => $defaultProduct->id,
                'quantity' => $quantity,
                'price' => $price,
                'sub_total' => $subTotal,
            ]);

            $order->notes()->create([
                'content' => $row['products'] ?? '',
                'user_id' => Auth::id(),
                'notable_id' => $order->id,
                'notable_type' => Order::class,
            ]);
        });
    }

    public function rules(): array
    {
        return [
            'phone' => ['required', 'digits:8', 'regex:/^[234579]\d{7}$/'],
            'client_name' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'state_id' => ['nullable', 'exists:states,id'],
            'products' => ['nullable', 'string'],
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'phone.regex' => 'The phone number must start with 2, 3, 4, 5, 7, or 9.',
            'phone.digits' => 'The phone field must be 8 digits.',
            'state_id.exists' => 'The selected state does not exist.',
        ];
    }

    public function onFailure(Failure ...$failures)
    {
        $this->failures = $failures;
    }

    public function chunkSize(): int
    {
        return 100;
    }
}
