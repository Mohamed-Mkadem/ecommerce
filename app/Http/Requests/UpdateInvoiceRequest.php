<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class UpdateInvoiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->role == 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        return [
            'title' => ['required', 'string', 'max:150', Rule::unique('invoices')->ignore($this->invoice->id)],
            'category' => ['required', 'string'],
            'type' => ['required', 'in:revenue,expense'],
            'description' => ['nullable', 'string', 'max:300'],
            'amount' => ['required', 'numeric', 'min:1'],
        ];
    }

    public function messages()
    {

        $messages = [
            'en' => [
                'title.required' => 'The title field is required.',
                'title.string' => 'The title must be a string.',
                'title.max' => 'The title may not be greater than 150 characters.',
                'title.unique' => 'The title has already been taken.',
                'category.required' => 'The category field is required.',
                'category.string' => 'The category must be a string.',
                'type.required' => 'The type field is required.',
                'type.in' => 'The selected type is invalid.',
                'description.string' => 'The description must be a string.',
                'description.max' => 'The description may not be greater than 300 characters.',
                'amount.required' => 'The amount field is required.',
                'amount.numeric' => 'The amount must be a number.',
                'amount.min' => 'The amount must be at least 100.',

            ],
            'fr' => [
                'title.required' => 'Le champ titre est obligatoire.',
                'title.string' => 'Le titre doit être une chaîne de caractères.',
                'title.max' => 'Le titre ne peut pas dépasser 150 caractères.',
                'title.unique' => 'Le titre a déjà été pris.',
                'category.required' => 'Le champ catégorie est obligatoire.',
                'category.string' => 'La catégorie doit être une chaîne de caractères.',
                'type.required' => 'Le champ type est obligatoire.',
                'type.in' => 'Le type sélectionné est invalide.',
                'description.string' => 'La description doit être une chaîne de caractères.',
                'description.max' => 'La description ne peut pas dépasser 300 caractères.',
                'amount.required' => 'Le champ montant est obligatoire.',
                'amount.numeric' => 'Le montant doit être un nombre.',
                'amount.min' => 'Le montant doit être d\'au moins 100.',
            ],
            'ar' => [
                'title.required' => 'حقل العنوان مطلوب.',
                'title.string' => 'يجب أن يكون العنوان نصًا.',
                'title.max' => 'لا يمكن أن يزيد العنوان عن 150 حرفًا.',
                'title.unique' => 'العنوان مأخوذ بالفعل.',
                'category.required' => 'حقل الفئة مطلوب.',
                'category.string' => 'يجب أن تكون الفئة نصًا.',
                'type.required' => 'حقل النوع مطلوب.',
                'type.in' => 'النوع المحدد غير صالح.',
                'description.string' => 'يجب أن تكون الوصف نصًا.',
                'description.max' => 'لا يمكن أن يزيد الوصف عن 300 حرفًا.',
                'amount.required' => 'حقل المبلغ مطلوب.',
                'amount.numeric' => 'يجب أن يكون المبلغ رقمًا.',
                'amount.min' => 'يجب أن يكون المبلغ على الأقل 100.',

            ],
        ];
        return $messages[app()->getLocale()];
    }
}
