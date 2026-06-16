<?php

namespace App\Http\Requests;

use App\Models\CouponCode;
use App\Models\Product;
use App\Models\State;
use Illuminate\Foundation\Http\FormRequest;

class PlaceOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {


        return [
            'name' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],

            'phone' => ['required', 'digits:8', 'regex:/^[234579]\d{7}$/'],
            'state' => ['nullable', 'array', 'required_array_keys:id,shipping_cost'],
            'state.id' => ['nullable', 'exists:states,id'],

            'note' => ['nullable', 'string', 'max:600'],

            'coupon' => ['nullable', 'array'],
            'coupon.id' => ['exists:coupon_codes,id'],

            'cart' => ['nullable', 'array'],
            'cart.*.id' => ['exists:products,id'],
            'cart.*.quantity' => ['nullable', 'numeric', 'min:.5'],
            'total' => ['nullable', 'numeric'],
            'free_shipping' => ['nullable', 'boolean'],

            'user_data' => ['nullable', 'array'],
            'user_data.phone' => ['nullable', 'string'],
            'user_data.name' => ['nullable', 'string'],
            'user_data.address' => ['nullable', 'string'],
            'user_data.state' => ['nullable', 'string'],
        ];
    }
    public function withValidator($validator)
    {


        $validator->after(function ($validator) {
            $cart = $this->cart;

            // If cart or total aren't provided, skip totals validation (fields are optional)
            if (!is_array($cart) || !$this->has('total')) {
                return;
            }

            $total = (int) ($this->total * 1000);

            // Check if any item in cart has free_shipping OR if the form-level flag is set
            $hasAnyFreeShipping = $this->boolean('free_shipping') ||
                collect($cart)->some(function ($item) {
                    return isset($item['free_shipping']) && $item['free_shipping'] === true;
                });

            $state = $this->state ?? State::find(1)?->toArray() ?? ['id' => 1, 'shipping_cost' => 0];
            $shippingCost = $hasAnyFreeShipping
                ? 0
                : (int) (($state['shipping_cost'] ?? 0) * 1000);
            $itemsTotal = 0;
            foreach ($cart as $key => $item) {
                $product = Product::find($item['id']);
                $itemsTotal += ($product->price * $item['quantity']);

                if ((int) ($item['price'] * 1000) != $product->price) {
                    $validator->errors()->add("cart.{$key}.price", $this->messages()['cart.*.price']);
                }
            }

            $couponCode = $this->coupon;

            if ($couponCode) {
                $serverCouponCode = CouponCode::where('value', $couponCode['value'])->first();

                if (!$serverCouponCode) {
                    $validator->errors()->add("coupon.id", $this->messages()['coupon.id.exists']);
                }

                $discount = 1 - $couponCode['value'] / 100;
                $itemsTotal = (int) $itemsTotal * $discount;
            }



            if (($itemsTotal + $shippingCost) != $total) {
                $validator->errors()->add('total', $this->messages()['total']);
                // $validator->errors()->add('total', "cart : {$itemsTotal}, Cost {$shippingCost}, total : {$total}");
            }
        });
    }
    public function messages(): array
    {
        $messages = [
            'ar' => [
                'phone.regex' => 'يجب أن يبدأ رقم الهاتف بـ 2 أو 3 أو 4 أو 5 أو 7 أو 9.',
                'phone.digits' => 'يجب أن يحتوي رقم الهاتف على 8 أرقام.',
                'name.max' => 'يجب ألا يتجاوز الاسم 255 حرفًا.',
                'address.max' => 'يجب ألا يتجاوز العنوان 500 حرف.',

                'cart.*.quantity.min' => 'الحد الأدنى للكمية المطلوبة للمنتج هو 0.5',
                'cart.*.id.exists' => 'هذا المنتج لم يعد متوفراً، يرجى إزالته من السلة أو المحاولة لاحقاً',

                'state.id.exists' => 'الولاية غير موجودة',

                'terms_accepted.accepted' => 'يجب عليك قبول الشروط والأحكام',

                'coupon.id.exists' => 'رمز الكوبون لم يعد متوفراً، يرجى إزالته',
                'total' => 'هناك مشكلة في احتساب الإجمالي. الرجاء تحديث سلّة المشتريات وإعادة المحاولة',
                'note.max' => 'لا يمكن أن يتجاوز عدد أحرف الملاحظة 600 حرف',

                'cart.*.price' => 'تم تحديث سعر المنتج، يرجى تحديث السلة'
            ],
            'fr' => [
                'phone.regex' => 'Le numéro de téléphone doit commencer par 2, 3, 4, 5, 7 ou 9.',
                'phone.digits' => 'Le numéro téléphone doit contenir 8 chiffres.',
                'name.max' => 'Le nom ne doit pas dépasser 255 caractères.',
                'address.max' => 'L\'adresse ne doit pas dépasser 500 caractères.',

                'cart.*.quantity.min' => 'La quantité minimale du produit est 0.5',
                'cart.*.id.exists' => 'Ce produit n\'est plus disponible, veuillez le retirer du panier ou réessayer plus tard',

                'state.id.exists' => "L'État n'existe pas",

                'terms_accepted.accepted' => 'Vous devez accepter les termes et conditions',

                'coupon.id.exists' => 'Le code promo n\'est plus disponible, veuillez le retirer',
                'total' => "Il y a un problème dans le calcul du total. Veuillez actualiser le panier et réessayer.",
                'note.max' => 'La note ne peut pas dépasser 600 caractères',

                'cart.*.price' => 'Le prix du produit a été mis à jour, veuillez mettre à jour votre panier'
            ],
            'en' => [
                'phone.regex' => 'The phone number must start with 2, 3, 4, 5, 7, or 9.',
                'phone.digits' => 'The phone field must be 8 digits.',
                'name.max' => 'The name must not exceed 255 characters.',
                'address.max' => 'The address must not exceed 500 characters.',

                'cart.*.quantity.min' => 'The minimum product quantity is 0.5',
                'cart.*.id.exists' => 'This product is no longer available, please remove it from the cart or try later',

                'state.id.exists' => "The State doesn't exist",

                'terms_accepted.accepted' => 'You must accept the terms and conditions',

                'coupon.id.exists' => 'The coupon code is no longer available, please remove it',
                'total' => "There is an issue with calculating the total. Please refresh the cart and try again.",
                'note.max' => 'The note cannot be more than 600 characters',

                'cart.*.price' => 'The product price was updated, please update your cart'
            ],
        ];

        return $messages[app()->getLocale()];
    }
}
