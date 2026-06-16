<?php

namespace App\Http\Requests;

use App\Models\Product;
use App\Models\CouponCode;
use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderProductsRequest extends FormRequest
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
            'order_id' => ['required', 'exists:orders,id'],

            'cart' => ['required', 'array'],
            'cart.*.id' => ['exists:products,id'],
            'cart.*.quantity' => ['required', 'numeric', 'min:.5'],
            'total' => ['required', 'numeric'],
        ];
    }
    public function withValidator($validator)
    {


        $validator->after(function ($validator) {
            $cart = $this->cart;
            $total = (int) round($this->total * 1000);
            $shippingCost = (int) round($this->shipping_cost * 1000);
            $itemsTotal = 0;

            foreach ($cart as $key => $item) {
                $product = Product::find($item['id']);
                $itemsTotal += ($product->price * $item['quantity']);

                if ((int) round($item['price'] * 1000) != $product->price) {
                    $validator->errors()->add("cart.{$key}.price", $this->messages()['cart.*.price']);
                }
            }

            $couponCode = $this->coupon;

            if ($couponCode) {
                $serverCouponCode = CouponCode::find($couponCode);

                if (!$serverCouponCode) {
                    $validator->errors()->add("coupon", $this->messages()['coupon.exists']);
                }

                $discount = 1 - $serverCouponCode->value / 100;
                $itemsTotal = (int) $itemsTotal * $discount;
            }



            if (($itemsTotal + $shippingCost) != $total) {
                $validator->errors()->add('total', $this->messages()['total']);
            }
        });
    }

    public function messages()
    {
        $messages = [
            'ar' => [
                'cart.required' => 'الرجاء إضافة منتجات',
                'total' => 'المجموع غير مطابق',
                'cart.*.price' => 'تغير سعر المنتج',
                'coupon.exists' => 'رمز القسيمة غير صالح',
            ],
            'en' => [
                'cart.required' => 'Please add some products',
                'total' => 'Total amount mismatch',
                'cart.*.price' => 'Product price changed',
                'coupon.exists' => 'Invalid coupon code',
            ],
            'fr' => [
                'cart.required' => "Ajoutez quelques produits s'il vous plait",
                'total' => 'Total incorrect',
                'cart.*.price' => 'Le prix du produit a changé',
                'coupon.exists' => 'Code promo invalide',
            ],
        ];

        return $messages[app()->getLocale()];
    }
}
