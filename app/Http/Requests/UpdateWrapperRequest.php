<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateWrapperRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        $locales = ['en', 'ar', 'fr'];

        $rules = [
            'caption' => ['nullable', 'string', 'max:255'],
            'is_active' => ['required', 'boolean'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'deleted_media' => ['nullable', 'array'],
            'deleted_media.*' => ['integer', 'exists:media,id'],
            'products' => ['required', 'array', 'min:1'],
            'products.*.product_id' => ['required', 'integer', 'exists:products,id', 'distinct'],
            'products.*.display_order' => ['required', 'integer', 'min:0'],
            'products.*.is_default' => ['required', 'boolean'],
            'products.*.free_shipping' => ['required', 'boolean'],
            'products.*.update_quantity' => ['required', 'numeric', Rule::in([0.5, 1])],
        ];

        foreach ($locales as $locale) {
            $rules["{$locale}.title"] = ['required', 'string', 'max:255'];
            $rules["{$locale}.description"] = ['nullable', 'string'];
        }

        return $rules;
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $products = $this->input('products', []);
            $defaultCount = collect($products)->where('is_default', true)->count();

            if ($defaultCount !== 1) {
                $validator->errors()->add(
                    'products',
                    __('Wrapper.default_variant_required')
                );
            }
        });
    }
}
