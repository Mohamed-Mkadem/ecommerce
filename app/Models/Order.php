<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Nrp;
use App\Models\City;
use App\Models\Note;
use App\Models\State;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Shipper;
use App\Models\Locality;
use App\Models\CouponCode;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\LogOptions;
use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{

    use HasFactory, SoftDeletes, LogsActivity;
    protected $fillable = [
        'client_id',
        'state_id',
        'city_id',
        'locality_id',
        'coupon_code_id',
        'shipper_id',
        'status',
        'source',
        'amount',
        'shipping_cost',
        'client_name',
        'address',
        'note',
        'phone',
        'phone2',
        'delivery_date',
        'free_shipping',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(["*"]);
    }

    public function tapActivity(Activity $activity, string $eventName)
    {
        if ($eventName === 'deleted') {
            $activity->description = 'order.deleted';
            $activity->icon = 'ri-delete-bin-line';
        }
        if ($eventName === 'updated' && $this->isDirty('status')) {
            switch ($this->status) {
                case 'confirmed':
                    $activity->description = 'order.confirmed';
                    $activity->icon = 'ri-checkbox-fill';
                    break;
                case 'shipped':
                    $activity->description = 'order.shipped';
                    $activity->icon = 'ri-truck-fill';
                    break;
                case 'delivered':
                    $activity->description = 'order.delivered';
                    $activity->icon = 'ri-home-9-fill';
                    break;
                case 'canceled':
                    $activity->description = 'order.canceled';
                    $activity->icon = 'ri-close-circle-fill';
                    break;
                case 'returned':
                    $activity->description = 'order.returned';
                    $activity->icon = 'ri-arrow-go-back-line';
                    break;
                case 'abandoned':
                    $activity->description = 'order.abandoned';
                    $activity->icon = 'ri-time-line';
                    break;
                default:
                    $activity->description = 'order.created';
                    $activity->icon = 'ri-shopping-cart-fill';
            }
        } elseif ($eventName === 'updated' && !$this->isDirty('status')) {
            $activity->description = 'order.updated';
            $activity->icon = 'ri-edit-box-line';
        }
        if ($eventName === 'created') {
            switch ($this->status) {
                case 'confirmed':
                    $activity->description = 'order.confirmed';
                    $activity->icon = 'ri-checkbox-fill';
                    break;
                case 'shipped':
                    $activity->description = 'order.shipped';
                    $activity->icon = 'ri-truck-fill';
                    break;
                case 'delivered':
                    $activity->description = 'order.delivered';
                    $activity->icon = 'ri-home-9-fill';
                    break;
                case 'canceled':
                    $activity->description = 'order.canceled';
                    $activity->icon = 'ri-close-circle-fill';
                    break;
                case 'returned':
                    $activity->description = 'order.returned';
                    $activity->icon = 'ri-arrow-go-back-line';
                    break;
                case 'abandoned':
                    $activity->description = 'order.abandoned';
                    $activity->icon = 'ri-time-line';
                    break;
                default:
                    $activity->description = 'order.created';
                    $activity->icon = 'ri-shopping-cart-fill';
            }
        }
    }

    public function notes()
    {
        return $this->morphMany(Note::class, 'notable');
    }
    public function client()
    {
        return $this->belongsTo(Client::class)->withTrashed();
    }


    public function state()
    {
        return $this->belongsTo(State::class);
    }
    public function city()
    {
        return $this->belongsTo(City::class);
    }
    public function locality()
    {
        return $this->belongsTo(Locality::class);
    }


    public function couponCode()
    {
        return $this->belongsTo(CouponCode::class)->withTrashed();
    }


    public function shipper()
    {
        return $this->belongsTo(Shipper::class)->withTrashed();
    }

    public function products()
    {
        return $this->belongsToMany(Product::class)->withTrashed()->withPivot([
            'price',
            'quantity',
            'sub_total',
            'created_at'
        ])->using(OrderProduct::class);
    }



    public function invoices()
    {
        return $this->morphMany(Invoice::class, 'invoiceable');
    }

    public function nrp()
    {
        return $this->hasOne(Nrp::class);
    }

    public static function getCount()
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
            'total' => [
                'total' => self::count(),
                'pending'   => self::where('status', 'pending')->count(),
                'confirmed'   => self::where('status', 'confirmed')->count(),
                'canceled'   => self::where('status', 'canceled')->count(),
                'returned'   => self::where('status', 'returned')->count(),
                'delivered'   => self::where('status', 'delivered')->count(),
                'shipped'   => self::where('status', 'shipped')->count(),
                'abandoned'   => self::where('status', 'abandoned')->count(),
            ],
            'day' => [
                'total' => self::whereBetween('created_at', [$todayStart, $todayEnd])->count(),
                'pending' => self::where('status', 'pending')->whereBetween('created_at', [$todayStart, $todayEnd])->count(),
                'confirmed' => self::where('status', 'confirmed')->whereBetween('created_at', [$todayStart, $todayEnd])->count(),
                'shipped' => self::where('status', 'shipped')->whereBetween('created_at', [$todayStart, $todayEnd])->count(),
                'canceled' => self::where('status', 'canceled')->whereBetween('created_at', [$todayStart, $todayEnd])->count(),
                'returned' => self::where('status', 'returned')->whereBetween('created_at', [$todayStart, $todayEnd])->count(),
                'delivered' => self::where('status', 'delivered')->whereBetween('created_at', [$todayStart, $todayEnd])->count(),
                'abandoned' => self::where('status', 'abandoned')->whereBetween('created_at', [$todayStart, $todayEnd])->count(),

            ],
            'week' => [
                'total' => self::whereBetween('created_at', [$currentWeekStart, $currentWeekEnd])->count(),
                'pending' => self::where('status', 'pending')->whereBetween('created_at', [$currentWeekStart, $currentWeekEnd])->count(),
                'confirmed' => self::where('status', 'confirmed')->whereBetween('created_at', [$currentWeekStart, $currentWeekEnd])->count(),
                'shipped' => self::where('status', 'shipped')->whereBetween('created_at', [$currentWeekStart, $currentWeekEnd])->count(),
                'returned' => self::where('status', 'returned')->whereBetween('created_at', [$currentWeekStart, $currentWeekEnd])->count(),
                'canceled' => self::where('status', 'canceled')->whereBetween('created_at', [$currentWeekStart, $currentWeekEnd])->count(),
                'delivered' => self::where('status', 'delivered')->whereBetween('created_at', [$currentWeekStart, $currentWeekEnd])->count(),
                'abandoned' => self::where('status', 'abandoned')->whereBetween('created_at', [$currentWeekStart, $currentWeekEnd])->count(),
            ],
            'month' => [
                'total' => self::whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])->count(),
                'pending' => self::where('status', 'pending')->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])->count(),
                'confirmed' => self::where('status', 'confirmed')->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])->count(),
                'shipped' => self::where('status', 'shipped')->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])->count(),
                'returned' => self::where('status', 'returned')->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])->count(),
                'canceled' => self::where('status', 'canceled')->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])->count(),
                'delivered' => self::where('status', 'delivered')->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])->count(),
                'abandoned' => self::where('status', 'abandoned')->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])->count(),
            ],
            'year' => [
                'total' => self::whereBetween('created_at', [$currentYearStart, $currentYearEnd])->count(),
                'pending' => self::where('status', 'pending')->whereBetween('created_at', [$currentYearStart, $currentYearEnd])->count(),
                'confirmed' => self::where('status', 'confirmed')->whereBetween('created_at', [$currentYearStart, $currentYearEnd])->count(),
                'shipped' => self::where('status', 'shipped')->whereBetween('created_at', [$currentYearStart, $currentYearEnd])->count(),
                'returned' => self::where('status', 'returned')->whereBetween('created_at', [$currentYearStart, $currentYearEnd])->count(),
                'canceled' => self::where('status', 'canceled')->whereBetween('created_at', [$currentYearStart, $currentYearEnd])->count(),
                'delivered' => self::where('status', 'delivered')->whereBetween('created_at', [$currentYearStart, $currentYearEnd])->count(),
                'abandoned' => self::where('status', 'abandoned')->whereBetween('created_at', [$currentYearStart, $currentYearEnd])->count(),
            ],


        ];
    }
    public static function getOrdersByStates()
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
            'total' => self::getStateCounts(),
            'day' => self::getStateCounts($todayStart, $todayEnd),
            'week' => self::getStateCounts($currentWeekStart, $currentWeekEnd),
            'month' => self::getStateCounts($currentMonthStart, $currentMonthEnd),
            'year' => self::getStateCounts($currentYearStart, $currentYearEnd),
        ];
    }

    private static function getStateCounts($startDate = null, $endDate = null)
    {
        $locale = App::getLocale();


        $states = DB::table('states')
            ->join('state_translations', function ($join) use ($locale) {
                $join->on('states.id', '=', 'state_translations.state_id')
                    ->where('state_translations.locale', '=', $locale);
            })
            ->pluck('state_translations.name', 'states.id');


        $initializedResults = [];
        foreach ($states as $stateId => $stateName) {
            $initializedResults[$stateName] = [
                'state' => $stateName,
                'count' => 0,
            ];
        }


        $query = self::query()
            ->rightJoin('states', 'orders.state_id', '=', 'states.id')
            ->join('state_translations', function ($join) use ($locale) {
                $join->on('states.id', '=', 'state_translations.state_id')
                    ->where('state_translations.locale', '=', $locale);
            })
            ->groupBy('state_translations.name')
            ->selectRaw('state_translations.name as state, COALESCE(COUNT(orders.id), 0) as count');

        if ($startDate && $endDate) {
            $query->whereBetween('orders.created_at', [$startDate, $endDate]);
        }

        $results = $query->get()->keyBy('state')->toArray();


        foreach ($results as $state => $result) {
            $initializedResults[$state]['count'] = $result['count'];
        }


        $sortedResults = collect($initializedResults)->sortByDesc('count')->toArray();

        return $sortedResults;
    }

    public static function couponCodeUsagePercentage()
    {
        // Count the total number of orders
        //  $totalOrders = Order::count();

        //  // Count the number of orders with a coupon code
        //  $ordersWithCoupon = Order::whereNotNull('coupon_code_id')->count();

        //  // Calculate the percentage
        //  $percentageWithCoupon = $totalOrders > 0 ? ($ordersWithCoupon / $totalOrders) * 100 : 0;

        $todayStart = Carbon::today()->startOfDay();
        $todayEnd = Carbon::today()->endOfDay();

        $currentWeekStart = Carbon::now()->startOfWeek();
        $currentWeekEnd = Carbon::now()->endOfWeek();

        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();

        $currentYearStart = Carbon::now()->startOfYear();
        $currentYearEnd = Carbon::now()->endOfYear();
        return [
            'total' =>  round(self::calculatePercentage(), 2),
            'day' => round(self::calculatePercentage($todayStart, $todayEnd), 2),
            'week' =>  round(self::calculatePercentage($currentWeekStart, $currentWeekEnd), 2),
            'month' =>  round(self::calculatePercentage($currentMonthStart, $currentMonthEnd), 2),
            'year' =>  round(self::calculatePercentage($currentYearStart, $currentYearEnd), 2),
        ];
    }
    private static function calculatePercentage($startDate = null, $endDate = null)
    {



        if ($startDate && $endDate) {
            $totalOrders = self::whereBetween('created_at', [$startDate, $endDate])->count();
            $ordersWithCoupon = self::query()->whereNotNull('coupon_code_id')->whereBetween('created_at', [$startDate, $endDate])->count();

            return $totalOrders > 0 ? ($ordersWithCoupon / $totalOrders) * 100 : 0;
        } else {
            $totalOrders = self::count();
            $ordersWithCoupon = self::query()->whereNotNull('coupon_code_id')->count();

            return $totalOrders > 0 ? ($ordersWithCoupon / $totalOrders) * 100 : 0;
        }
    }
}
