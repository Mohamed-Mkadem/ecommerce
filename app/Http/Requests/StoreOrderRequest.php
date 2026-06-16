<?php

namespace App\Http\Requests;

use App\Models\CouponCode;
use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string'],

            'phone' => ['required', 'digits:8', 'regex:/^[234579]\d{7}$/'],
            'phone2' => ['nullable', 'digits:8', 'regex:/^[234579]\d{7}$/'],
            'state' => ['required', 'exists:states,id'],
            'city' => ['required', 'exists:cities,id'],
            'locality' => ['required', 'exists:localities,id'],
            'shipper' => ['required', 'exists:shippers,id'],


            'note' => ['nullable', 'string', 'max:600'],

            'coupon' => ['nullable', 'exists:coupon_codes,id'],
            'deliveryDate' => ['required', 'date'],

            'cart' => ['required', 'array'],
            'cart.*.id' => ['exists:products,id'],
            'cart.*.quantity' => ['required', 'numeric', 'min:.5'],
            'total' => ['required', 'numeric'],
            'status' => ['required', 'in:pending,shipped,delivered,returned,confirmed,canceled,abandoned'],
            'nrp' => ['nullable', 'boolean'],
            'free_shipping' => ['nullable', 'boolean'],

        ];
    }
    public function withValidator($validator)
    {


        $validator->after(function ($validator) {
            $cart = $this->cart;
            $total = (int) ($this->total * 1000);
            $shippingCost = (int) ($this->shipping_cost * 1000);
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
    public function messages(): array
    {
        $messages = [
            'ar' => [
                'phone2.regex' => 'يجب أن يبدأ رقم الهاتف بـ 2 أو 3 أو 4 أو 5 أو 7 أو 9.',
                'phone2.digits' => 'يجب أن يحتوي رقم الهاتف على 8 أرقام.',
                'phone.regex' => 'يجب أن يبدأ رقم الهاتف بـ 2 أو 3 أو 4 أو 5 أو 7 أو 9.',
                'phone.digits' => 'يجب أن يحتوي رقم الهاتف على 8 أرقام.',
                'name.max' => 'يجب ألا يتجاوز الاسم 255 حرفًا.',
                'address.max' => 'يجب ألا يتجاوز العنوان 500 حرف.',

                'cart.*.quantity.min' => 'الحد الأدنى للكمية المطلوبة للمنتج هو 1',
                'cart.*.id.exists' => 'هذا المنتج لم يعد متوفراً، يرجى إزالته من السلة أو المحاولة لاحقاً',

                'state.exists' => 'الولاية غير موجودة',


                'coupon.exists' => 'رمز الكوبون لم يعد متوفراً، يرجى إزالته',
                'total' => 'هناك مشكلة في احتساب الإجمالي. الرجاء تحديث سلّة المشتريات وإعادة المحاولة',
                'note.max' => 'لا يمكن أن يتجاوز عدد أحرف الملاحظة 600 حرف',

                'cart.*.price' => 'تم تحديث سعر المنتج، يرجى تحديث السلة',
                'cart.required' => 'السلة لا يمكن أن تكون فارغة',
                'deliveryDate.after' => 'تاريخ التسليم يجب أن يكون في المستقبل',
                'status.in' => 'الحالة يجب أن تكون من بين: قيد الإنتظار, تمّ الشحن, تمّ الاستلام, تمّ الإرجاع, تمّ التأكيد, تمّ الإلغاء',
                'shipper.exists' => 'شكرة الشحن غير موجودة',

            ],
            'fr' => [
                'phone.regex' => 'Le numéro de téléphone doit commencer par 2, 3, 4, 5, 7 ou 9.',
                'phone.digits' => 'Le numéro téléphone doit contenir 8 chiffres.',
                'name.max' => 'Le nom ne doit pas dépasser 255 caractères.',
                'address.max' => 'L\'adresse ne doit pas dépasser 500 caractères.',

                'cart.*.quantity.min' => 'La quantité minimale du produit est 1',
                'cart.*.id.exists' => 'Ce produit n\'est plus disponible, veuillez le retirer du panier ou réessayer plus tard',

                'state.exists' => "L'État n'existe pas",


                'coupon.exists' => 'Le code promo n\'est plus disponible, veuillez le retirer',
                'total' => "Il y a un problème dans le calcul du total. Veuillez actualiser le panier et réessayer.",
                'note.max' => 'La note ne peut pas dépasser 600 caractères',

                'cart.*.price' => 'Le prix du produit a été mis à jour, veuillez mettre à jour votre panier',
                'cart.required' => 'Le panier ne peut pas être vide',
                'deliveryDate.after' => 'La date de livraison doit être dans le futur',
                'status.in' => 'Le statut doit être parmi: en attente, expédié, livré, retourné, confirmé, annulé',
                'shipper.exists' => 'Le Livreur n\'existe pas',
                'phone2.regex' => 'Le numéro de téléphone doit commencer par 2, 3, 4, 5, 7 ou 9.',
                'phone2.digits' => 'Le numéro téléphone doit contenir 8 chiffres.',

            ],
            'en' => [
                'phone2.regex' => 'The phone number must start with 2, 3, 4, 5, 7, or 9.',
                'phone2.digits' => 'The phone field must be 8 digits.',
                'phone.regex' => 'The phone number must start with 2, 3, 4, 5, 7, or 9.',
                'phone.digits' => 'The phone field must be 8 digits.',
                'name.max' => 'The name must not exceed 255 characters.',
                'address.max' => 'The address must not exceed 500 characters.',

                'cart.*.quantity.min' => 'The minimum product quantity is 1',
                'cart.*.id.exists' => 'This product is no longer available, please remove it from the cart or try later',

                'state.id.exists' => "The State doesn't exist",


                'coupon.exists' => 'The coupon code is no longer available, please remove it',
                'total' => "There is an issue with calculating the total. Please refresh the cart and try again.",
                'note.max' => 'The note cannot be more than 600 characters',

                'cart.*.price' => 'The product price was updated, please update your cart',
                'cart.required' => 'The cart cannot be empty',
                'deliveryDate.after' => 'The delivery date must be in the future',
                'status.in' => 'The status must be one of: pending, shipped, delivered, returned, confirmed, canceled, abandoned',
                'shipper.exists' => 'The Shipper does not exist',

            ],
        ];

        return $messages[app()->getLocale()];
    }
}
