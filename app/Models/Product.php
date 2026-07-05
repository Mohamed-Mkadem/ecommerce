<?php

namespace App\Models;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Wrapper;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;


class Product extends Model implements TranslatableContract
{
    use HasFactory, SoftDeletes, Translatable,  LogsActivity;

    protected $fillable = [
        'price',
        'shipping_name',
        'discount',
        'discount_type'
    ];

    public $translatedAttributes = ['name'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(["*"]);
    }

    public function tapActivity(Activity $activity, string $eventName)
    {
        if ($eventName === 'updated') {
            $activity->description = 'product.updated';
            $activity->icon = 'ri-edit-box-line';
        }
        if ($eventName === 'created') {
            $activity->description = 'product.created';
            $activity->icon = 'ri-add-box-line';
        }
        if ($eventName === 'deleted') {
            $activity->description = 'product.deleted';
            $activity->icon = 'ri-delete-bin-line';
        }
    }





    public function getFormattedPrice()
    {
        return number_format($this->price / 1000, 3, '.', '');
    }


    public function wrappers()
    {
        return $this->belongsToMany(Wrapper::class, 'product_wrapper')
            ->using(ProductWrapper::class)
            ->withPivot('display_order', 'is_default', 'free_shipping');
    }




    public function orders()
    {
        return $this->belongsToMany(Order::class)->withPivot([
            'price',
            'quantity',
            'sub_total',
            'created_at'
        ])->using(OrderProduct::class);
    }


    public function orderItems()
    {
        return $this->hasMany(OrderProduct::class);
    }




    public static function getBestSelling()
    {
        $todayStart = Carbon::today()->startOfDay();
        $todayEnd = Carbon::today()->endOfDay();

        $currentWeekStart = Carbon::now()->startOfWeek();
        $currentWeekEnd = Carbon::now()->endOfWeek();

        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();

        $currentYearStart = Carbon::now()->startOfYear();
        $currentYearEnd = Carbon::now()->endOfYear();


        return [
            'total' =>   self::query()
                ->addSelect(['total_orders_count' => function ($query) {
                    $query->selectRaw('SUM(order_product.quantity)')
                        ->from('order_product')
                        ->join('orders', 'order_product.order_id', '=', 'orders.id')
                        ->whereColumn('order_product.product_id', 'products.id')
                        ->where('orders.status', 'delivered');
                }])
                ->orderBy('total_orders_count', 'desc')
                ->take(10)
                ->get()

                ->map(fn($product) => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->getFormattedPrice(),
                    'translations' => $product->translations,
                    'delivered_orders_count' => $product->orders()->where('status', 'delivered')->sum('order_product.quantity'),
                    'main_image_url' => asset('storage/products/product.webp'),
                ]),

            'day' =>  self::query()
                ->addSelect(['total_orders_count' => function ($query) use ($todayEnd, $todayStart) {
                    $query->selectRaw('SUM(order_product.quantity)')
                        ->from('order_product')
                        ->join('orders', 'order_product.order_id', '=', 'orders.id')
                        ->whereColumn('order_product.product_id', 'products.id')
                        ->where('orders.status', 'delivered')
                        ->whereBetween('orders.created_at', [$todayStart, $todayEnd]);
                }])
                ->orderBy('total_orders_count', 'desc')

                ->take(10)
                ->get()
                ->map(fn($product) => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->getFormattedPrice(),
                    'translations' => $product->translations,
                    'delivered_orders_count' => $product->orders()->where('status', 'delivered')
                        ->whereBetween('orders.created_at', [$todayStart, $todayEnd])
                        ->sum('order_product.quantity'),
                    'main_image_url' => asset('storage/products/product.webp'),
                ]),
            'week' =>  self::query()
                ->addSelect(['total_orders_count' => function ($query) use ($currentWeekEnd, $currentWeekStart) {
                    $query->selectRaw('SUM(order_product.quantity)')
                        ->from('order_product')
                        ->join('orders', 'order_product.order_id', '=', 'orders.id')
                        ->whereColumn('order_product.product_id', 'products.id')
                        ->where('orders.status', 'delivered')
                        ->whereBetween('orders.created_at', [$currentWeekStart, $currentWeekEnd]);
                }])
                ->orderBy('total_orders_count', 'desc')
                ->take(10)
                ->get()
                ->map(fn($product) => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->getFormattedPrice(),
                    'translations' => $product->translations,
                    'delivered_orders_count' => $product->orders()->where('status', 'delivered')
                        ->whereBetween('orders.created_at', [$currentWeekStart, $currentWeekEnd])
                        ->sum('order_product.quantity'),
                    'main_image_url' => asset('storage/products/product.webp'),
                ]),
            'month' =>  self::query()
                ->addSelect(['total_orders_count' => function ($query) use ($currentMonthEnd, $currentMonthStart) {
                    $query->selectRaw('SUM(order_product.quantity)')
                        ->from('order_product')
                        ->join('orders', 'order_product.order_id', '=', 'orders.id')
                        ->whereColumn('order_product.product_id', 'products.id')
                        ->where('orders.status', 'delivered')
                        ->whereBetween('orders.created_at', [$currentMonthStart, $currentMonthEnd]);
                }])
                ->orderBy('total_orders_count', 'desc')
                ->take(10)
                ->get()
                ->map(fn($product) => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->getFormattedPrice(),
                    'translations' => $product->translations,
                    'delivered_orders_count' => $product->orders()->where('status', 'delivered')
                        ->whereBetween('orders.created_at', [$currentMonthStart, $currentMonthEnd])
                        ->sum('order_product.quantity'),
                    'main_image_url' => asset('storage/products/product.webp'),
                ]),
            'year' =>  self::query()
                ->addSelect(['total_orders_count' => function ($query) use ($currentYearEnd, $currentYearStart) {
                    $query->selectRaw('SUM(order_product.quantity)')
                        ->from('order_product')
                        ->join('orders', 'order_product.order_id', '=', 'orders.id')
                        ->whereColumn('order_product.product_id', 'products.id')
                        ->where('orders.status', 'delivered')
                        ->whereBetween('orders.created_at', [$currentYearStart, $currentYearEnd]);
                }])
                ->orderBy('total_orders_count', 'desc')
                ->take(10)
                ->get()
                ->map(fn($product) => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->getFormattedPrice(),
                    'translations' => $product->translations,
                    'delivered_orders_count' => $product->orders()->where('status', 'delivered')
                        ->whereBetween('orders.created_at', [$currentYearStart, $currentYearEnd])
                        ->sum('order_product.quantity'),
                    'main_image_url' => asset('storage/products/product.webp'),
                ]),



        ];
    }
}
