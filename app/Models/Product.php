<?php

namespace App\Models;

use App\Http\Resources\ProductStatisticsResource;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Review;
use App\Models\Wrapper;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Product extends Model implements TranslatableContract, HasMedia
{
    use HasFactory, SoftDeletes, Translatable, InteractsWithMedia, LogsActivity;

    protected $fillable = [
        'type',
        'status',
        'price',
        'ends_at',
        'shipping_name',
        'discount',
        'discount_type'
    ];

    public $translatedAttributes = ['name', 'description'];

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



    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images');
    }

    public function getFormattedPrice()
    {
        return number_format($this->price / 1000, 3, '.', '');
    }
    public function getFormattedEndsAtDate()
    {
        return $this->type == 'pack' && $this->ends_at
            ? \Carbon\Carbon::parse($this->ends_at)->format('d-m-Y')
            : null;
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

    protected function mainImage(): Attribute
    {
        return Attribute::make(
            get: fn(string $value) => $this->getFirstMediaUrl('images') ?: asset('storage/products/default.png'),
        );
    }
    public function orderItems()
    {
        return $this->hasMany(OrderProduct::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function updateRate()
    {
        $rateAvg = round($this->reviews()->avg('stars'), 2);
        $this->rate = $rateAvg;
        $this->save();
    }

    public static function packsToEndTomorrow()
    {
        return self::where('type', 'pack')
            ->whereDate('ends_at', Carbon::tomorrow())
            ->get();
    }

    public static function getStatusCount()
    {
        return [
            'total' => self::where('type', 'product')->count(),
            'published' => self::where('type', 'product')->where('status', 'published')->count(),
            'hidden' => self::where('type', 'product')->where('status', 'hidden')->count(),
        ];
    }
    public static function getPacksStatusCount()
    {
        return [
            'total' => self::where('type', 'pack')->count(),
            'published' => self::where('type', 'pack')->where('status', 'published')->count(),
            'hidden' => self::where('type', 'pack')->where('status', 'hidden')->count(),
        ];
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
                ->where('type', 'product')
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
                    'main_image_url' => $product->getFirstMediaUrl('images') ?: asset('storage/products/default.png'),
                    'translations' => $product->translations,
                    'delivered_orders_count' => $product->orders()->where('status', 'delivered')->sum('order_product.quantity'),
                    'status' => $product->status,
                    'rate' => $product->rate,
                ]),

            'day' =>  self::query()
                ->where('type', 'product')
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
                    'main_image_url' => $product->getFirstMediaUrl('images') ?: asset('storage/products/default.png'),
                    'translations' => $product->translations,
                    'delivered_orders_count' => $product->orders()->where('status', 'delivered')
                        ->whereBetween('orders.created_at', [$todayStart, $todayEnd])
                        ->sum('order_product.quantity'),
                    'status' => $product->status,
                    'rate' => $product->rate,
                ]),
            'week' =>  self::query()
                ->where('type', 'product')
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
                    'main_image_url' => $product->getFirstMediaUrl('images') ?: asset('storage/products/default.png'),
                    'translations' => $product->translations,
                    'delivered_orders_count' => $product->orders()->where('status', 'delivered')
                        ->whereBetween('orders.created_at', [$currentWeekStart, $currentWeekEnd])
                        ->sum('order_product.quantity'),
                    'status' => $product->status,
                    'rate' => $product->rate,
                ]),
            'month' =>  self::query()
                ->where('type', 'product')
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
                    'main_image_url' => $product->getFirstMediaUrl('images') ?: asset('storage/products/default.png'),
                    'translations' => $product->translations,
                    'delivered_orders_count' => $product->orders()->where('status', 'delivered')
                        ->whereBetween('orders.created_at', [$currentMonthStart, $currentMonthEnd])
                        ->sum('order_product.quantity'),
                    'status' => $product->status,
                    'rate' => $product->rate,
                ]),
            'year' =>  self::query()
                ->where('type', 'product')
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
                    'main_image_url' => $product->getFirstMediaUrl('images') ?: asset('storage/products/default.png'),
                    'translations' => $product->translations,
                    'delivered_orders_count' => $product->orders()->where('status', 'delivered')
                        ->whereBetween('orders.created_at', [$currentYearStart, $currentYearEnd])
                        ->sum('order_product.quantity'),
                    'status' => $product->status,
                    'rate' => $product->rate,
                ]),



        ];
    }
    public static function getPacksBestSelling()
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
                ->where('type', 'pack')
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
                    'main_image_url' => $product->getFirstMediaUrl('images') ?: asset('storage/products/default.png'),
                    'translations' => $product->translations,
                    'delivered_orders_count' => $product->orders()->where('status', 'delivered')->sum('order_product.quantity'),
                    'status' => $product->status,
                    'rate' => $product->rate,
                    'ends_at' => $product->ends_at
                ]),

            'day' =>  self::query()
                ->where('type', 'pack')
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
                    'main_image_url' => $product->getFirstMediaUrl('images') ?: asset('storage/products/default.png'),
                    'translations' => $product->translations,
                    'delivered_orders_count' => $product->orders()->where('status', 'delivered')
                        ->whereBetween('orders.created_at', [$todayStart, $todayEnd])
                        ->sum('order_product.quantity'),
                    'status' => $product->status,
                    'rate' => $product->rate,
                    'ends_at' => $product->ends_at
                ]),
            'week' =>  self::query()
                ->where('type', 'pack')
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
                    'main_image_url' => $product->getFirstMediaUrl('images') ?: asset('storage/products/default.png'),
                    'translations' => $product->translations,
                    'delivered_orders_count' => $product->orders()->where('status', 'delivered')
                        ->whereBetween('orders.created_at', [$currentWeekStart, $currentWeekEnd])
                        ->sum('order_product.quantity'),
                    'status' => $product->status,
                    'rate' => $product->rate,
                    'ends_at' => $product->ends_at
                ]),
            'month' =>  self::query()
                ->where('type', 'pack')
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
                    'main_image_url' => $product->getFirstMediaUrl('images') ?: asset('storage/products/default.png'),
                    'translations' => $product->translations,
                    'delivered_orders_count' => $product->orders()->where('status', 'delivered')
                        ->whereBetween('orders.created_at', [$currentMonthStart, $currentMonthEnd])
                        ->sum('order_product.quantity'),
                    'status' => $product->status,
                    'rate' => $product->rate,
                    'ends_at' => $product->ends_at
                ]),
            'year' =>  self::query()
                ->where('type', 'pack')
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
                    'main_image_url' => $product->getFirstMediaUrl('images') ?: asset('storage/products/default.png'),
                    'translations' => $product->translations,
                    'delivered_orders_count' => $product->orders()->where('status', 'delivered')
                        ->whereBetween('orders.created_at', [$currentYearStart, $currentYearEnd])
                        ->sum('order_product.quantity'),
                    'status' => $product->status,
                    'rate' => $product->rate,
                    'ends_at' => $product->ends_at
                ]),



        ];
    }
}
