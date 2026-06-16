<?php

namespace App\Http\Requests\Product;

use Illuminate\Validation\Rules\File;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
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

        $locales = ['en', 'ar', 'fr'];

        $rules = [
            "price" => ['required', 'numeric', 'min:1'],
            "type" => ['required', 'in:pack,product'],
            "status" => ['required', 'in:published,hidden'],
            "ends_at" => ['required_if:type,pack', 'nullable', 'date', 'after:today', 'date_format:Y-m-d'],
            'images' => ['required', 'array'],
            'shipping_name' => ['required', 'string', 'max:100'],
            'images.*' => [
                'image',
                'mimes:jpeg,png,jpg,webp',
                'max:2048',
                Rule::dimensions()->height(1350)->width(1350)
            ],
            'discount_type' => ['nullable', 'in:percentage,fixed'],
            'discount' => ['nullable', 'numeric', 'min:0'],
        ];
        foreach ($locales as $locale) {
            $rules["$locale.name"] = ['required', 'string', 'max:350'];
            $rules["$locale.description"] = [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (trim(strip_tags($value)) === '') {
                        $fail(__('The field cannot be empty.'));
                    }
                }
            ];
        }
        return $rules;
    }

    public function messages()
    {


        $messages = [
            'ar' => [
                'ends_at.after' => 'حقل الصلوحية يجب أن يكون في المستقبل',
                'ends_at.required_if' => 'حقل الصلوحية يصبح إجباريا إذا كان نوع المنتج يساوي -عرضا-. ',
                'status.in' => 'حالة المنتج يجب أن تكون -منشور- أو -غير منشور-',
                'type.in' => 'نوع المنتج يجب أن تكون -منتج- أو - عرض-',
                'price.min' => 'سعر المنتج يجب أن يكون 1000 مليم على الأقل',
                'images.required' => 'يرجى تحميل صورة واحدة على الأقل.',
                'images.*.image' => 'يجب أن يكون كل ملف عبارة عن صورة.',
                'images.*.mimes' => 'يُسمح فقط بتنسيقات JPG وPNG.',
                'images.*.max' => 'يجب ألا يتجاوز حجم كل صورة 2 ميجابايت.',
                "shipping_name.required" => 'اسم الشحن مطلوب.',
                "shipping_name.string" => 'اسم الشحن يجب أن يكون نصًا.',
                "shipping_name.max" => 'اسم الشحن يجب ألا يتجاوز 100 حرف.',
                "discount.min" => "يجب أن يكون الخصم رقمًا موجبًا.",
            ],
            'fr' => [
                'ends_at.after' => 'Le champ de validité doit être une date future.',
                'ends_at.required_if' => 'Le champ de validité devient obligatoire si le type du produit est -Pack-.',
                'status.in' => 'Le statut du produit doit être -Publié- ou -Non publié-.',
                'type.in' => 'Le type de produit doit être -Produit- ou -Pack-.',
                'price.min' => "Le prix du produit doit être d'au moins 1000 millimes.",
                'images.required' => 'Veuillez télécharger au moins une image.',
                'images.*.image' => 'Chaque fichier doit être une image.',
                'images.*.mimes' => 'Seuls les formats JPG et PNG sont autorisés.',
                'images.*.max' => 'Chaque image ne doit pas dépasser 2 Mo.',
                "shipping_name.required" => 'Le nom de l\'expédition est requis.',
                "shipping_name.string" => 'Le nom de l\'expédition doit être une chaîne de caractères.',
                "shipping_name.max" => 'Le nom de l\'expédition ne doit pas dépasser 100 caractères.',
                "discount.min" => "La remise doit être un nombre positif.",
            ],

            'en' => [
                'ends_at.after' => 'The :attribute fiels must be in the future',
                'images.required' => 'Please upload at least one image.',
                'images.*.image' => 'Each file must be an image.',
                'images.*.mimes' => 'Only JPG and PNG formats are allowed.',
                'images.*.max' => 'Each image must not exceed 2MB.',
                "shipping_name.required" => 'Shipping name is required.',
                "shipping_name.string" => 'Shipping name must be a string.',
                "shipping_name.max" => 'Shipping name must not exceed 100 characters.',
                "discount.min" => "Discount must be a positive number.",
            ]
        ];
        return $messages[app()->getLocale()];
    }
}
