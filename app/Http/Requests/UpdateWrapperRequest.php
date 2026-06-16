<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateWrapperRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'caption' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
            'products' => ['required', 'array', 'min:1'],
            'products.*.product_id' => ['required', 'integer', 'exists:products,id', 'distinct'],
            'products.*.display_order' => ['required', 'integer', 'min:0'],
            'products.*.is_default' => ['required', 'boolean'],
            'products.*.free_shipping' => ['required', 'boolean'],
        ];
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
