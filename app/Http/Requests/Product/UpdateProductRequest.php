<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return in_array($this->user()->role, ['admin']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        $locales = ['en', 'ar', 'fr'];

        $rules = [
            "price" => ['required', 'numeric', 'min:1'],
            'shipping_name' => ['required', 'string', 'max:100'],
            'discount_type' => ['nullable', 'in:percentage,fixed'],
            'discount' => ['nullable', 'numeric', 'min:0'],
        ];
        foreach ($locales as $locale) {
            $rules["$locale.name"] = ['required', 'string', 'max:350'];
        }
        return $rules;
    }

    public function messages()
    {


        $messages = [
            'ar' => [
                "shipping_name.required" => 'اسم الشحن مطلوب.',
                "shipping_name.string" => 'اسم الشحن يجب أن يكون نصًا.',
                "shipping_name.max" => 'اسم الشحن يجب ألا يتجاوز 100 حرف.',
                "discount.min" => "يجب أن يكون الخصم رقمًا موجبًا.",

            ],
            'fr' => [

                'price.min' => "Le prix du produit doit être d'au moins 1000 millimes.",
                "shipping_name.required" => 'Le nom de l\'expédition est requis.',
                "shipping_name.string" => 'Le nom de l\'expédition doit être une chaîne de caractères.',
                "shipping_name.max" => 'Le nom de l\'expédition ne doit pas dépasser 100 caractères.',
                "discount.min" => "La remise doit être un nombre positif.",

            ],

            'en' => [

                "shipping_name.required" => 'Shipping name is required.',
                "shipping_name.string" => 'Shipping name must be a string.',
                "shipping_name.max" => 'Shipping name must not exceed 100 characters.',
                "discount.min" => "Discount must be a positive number.",
            ]
        ];
        return $messages[app()->getLocale()];
    }
}
