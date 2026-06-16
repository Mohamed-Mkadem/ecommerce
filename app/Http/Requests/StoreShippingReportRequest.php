<?php

namespace App\Http\Requests;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;

class StoreShippingReportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return in_array($this->user()->role, ['admin', 'moderator']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:shipping_reports,name'],
            'date' => ['required', 'date'],
            'shipper_id' => ['required'],
        ];
    }

    public function withValidator($validator)
    {

        $validator->after(function ($validator) {
            $ordersCount = Order::where('delivery_date', $this->date)->where('status', 'confirmed')->count();

            if (!$ordersCount && $this->date != null) {
                $validator->errors()->add('date.count', $this->messages()['date.count']);
            }
        });
    }


    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {

        $messages = [
            'en' => [
                'name.required' => 'The name field is required.',
                'name.string' => 'The name field must be a string.',
                'name.max' => 'The name field must not exceed 255 characters.',
                'date.required' => 'The delivery date field is required.',
                'date.date' => 'The date field must be a date.',
                'date.count' => 'There is no confirmed orders to ship for the specified date',
                'shipper_id.required' => "The shipper field is required.",

            ],
            'fr' => [
                'name.unique' => 'Le nom est déja utilisé.',
                'name.required' => 'Le nom est obligatoire.',
                'name.string' => 'Le nom doit être une chaîne de caractères.',
                'name.max' => 'Le nom ne doit pas dépasser 255 caractères.',
                'date.required' => 'La date de livraison est obligatoire.',
                'date.date' => 'La date de livraison doit être une date.',
                'date.count' =>
                'Il n\'y a pas de commandes confirmées à expédier pour la date spécifiée',
                'shipper_id.required' => "Le livreur est obligatoire.",
            ],
            'ar' => [
                'name.unique' => 'تمّ استعمال هذا الإسم من قبل',
                'name.required' => 'حقل الاسم إجباري.',
                'name.string' => 'يجب أن يكون حقل الاسم نصّا.',
                'name.max' => 'يجب ألا يتجاوز حقل الاسم 255 حرفًا.',
                'date.required' => 'حقل التاريخ إجباري.',
                'date.date' => 'يجب أن يكون حقل التاريخ تاريخًا.',
                'date.count' => 'لا يوجد طلبات تمّ تأكيدها سيتمّ شحنها في التاريخ المحدّد',
                'shipper_id.required' => "حقل شركة الشحن إجباري.",
            ]
        ];
        return $messages[app()->getLocale()];
    }
}
