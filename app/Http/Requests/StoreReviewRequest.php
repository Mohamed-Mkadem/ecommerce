<?php

namespace App\Http\Requests;

use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
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
            'orderID' => ['required', 'exists:orders,id'],
            'name' => ['required', 'string', 'max:50'],
            'stars' => ['required', 'between:1,5', 'numeric'],
            'comment' => ['nullable', 'string', 'max:255'],
            'productID' => ['required', 'exists:products,id']
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $order = Order::find($this->orderID);

            if (!$order) {
                $validator->errors()->add('order_number', $this->messages()['orderID.exists']);
                return;
            }
            $product = Product::find($this->productID);
            if (!$product) {
                $validator->errors()->add('product.missing', $this->messages()['product.missing']);
                return;
            }
            $client_id = $order->client_id;
            $oldReview = Review::where('client_id', $client_id)->where('product_id', $this->productID)->first();
            if ($order && $order->status != 'delivered') {
                $validator->errors()->add('order.status', $this->messages()['order.status']);
            }
            if ($product && !$order->products->pluck('id')->contains($product->id)) {
                $validator->errors()->add('product.missing', $this->messages()['product.missing']);
            }
            if ($oldReview) {

                $validator->errors()->add('product.reviewed', $this->messages()['product.reviewed']);
            }
        });
    }

    public function messages()
    {
        $messages = [
            'fr' => [
                'orderID.exists' => 'nous ne pouvons pas trouver une commande avec ce numéro',
                'productID.exists' => 'nous ne pouvons pas évaluer ce produit maintenant, veuillez réessayer plus tard',
                'stars.between' => 'Le nombre d\'étoiles doit être compris entre 1 et 5',
                'comment.max' => 'Le nombre maximum de caractères pour le commentaire est de 255',
                'name.max' => 'Le nombre maximum de caractères pour le nom est de 50',
                'order.status' => 'Seuls les produits livrés peuvent être évalués',
                'product.missing' => 'Ce produit n\'existe pas dans la liste des produits de la commande',
                'product.reviewed' => 'Vous avez déjà évalué ce produit'
            ],
            'ar' => [
                'orderID.exists' => 'لا يمكننا العثور على طلب بهذا الرقم',
                'productID.exists' => 'لا يمكننا تقييم هذا المنتج الآن، يرجى المحاولة لاحقًا',
                'stars.between' => 'يجب أن تكون التقييمات بين 1 و 5',
                'comment.max' => 'الحد الأقصى لعدد الأحرف للتعليق هو 255',
                'name.max' => 'الحد الأقصى لعدد الأحرف للاسم هو 50',
                'order.status' => 'يمكن تقييم المنتجات التي تم تسليمها فقط',
                'product.missing' => 'هذا المنتج غير موجود في قائمة المنتجات الخاصة بالطلب',
                'product.reviewed' => 'لقد قمت بتقييم هذا المنتج بالفعل'
            ],
            'en' => [
                'orderID.exists' => 'we cannot find an order with that number',
                'orderID.required' => 'The Order Number is required',
                'productID.exists' => 'we cannot review this product now, please try later',
                'stars.between' => 'The review must be between 1 and 5',
                'comment.max' => 'The maximum number of characters for the comment is 255',
                'name.max' => 'The maximum number of characters for the name is 50',
                'name.required' => 'The name field is required',
                'order.status' => 'Only delivered products can be reviewed',
                'product.missing' => 'This product doesn\'t exist on the products list of the order',
                'product.reviewed' => 'You are already reviewed this product',
            ]
        ];

        return $messages[app()->getLocale()];
    }
}
