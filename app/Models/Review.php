<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Client;
use App\Models\Product;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class Review extends Model
{

    use LogsActivity;
    protected $fillable = [
        'client_id',
        'product_id',
        'stars',
        'comment',
        'client_name'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(["*"]);
    }

    public function tapActivity(Activity $activity, string $eventName)
    {
        if ($eventName !== 'deleted') {
            activity()->disableLogging();
            return;
        } else {
            $activity->description = 'review.deleted';
            $activity->icon = 'ri-delete-bin-line';
        }
    }


    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function client()
    {
        return $this->belongsTo(Client::class)->withTrashed();
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
                'day' => self::whereBetween('created_at', [$todayStart, $todayEnd])->count(),
                'week' => self::whereBetween('created_at', [$currentWeekStart, $currentWeekEnd])->count(),
                'month' => self::whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])->count(),
                'year' => self::whereBetween('created_at', [$currentYearStart, $currentYearEnd])->count(),
            ],
            'one' => [
                'total' => self::where('stars', 1)->count(),
                'day' => self::where('stars', 1)->whereBetween('created_at', [$todayStart, $todayEnd])->count(),
                'week' => self::where('stars', 1)->whereBetween('created_at', [$currentWeekStart, $currentWeekEnd])->count(),
                'month' => self::where('stars', 1)->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])->count(),
                'year' => self::where('stars', 1)->whereBetween('created_at', [$currentYearStart, $currentYearEnd])->count(),

            ],

            'two' => [
                'total' => self::where('stars', 2)->count(),
                'day' => self::where('stars', 2)->whereBetween('created_at', [$todayStart, $todayEnd])->count(),
                'week' => self::where('stars', 2)->whereBetween('created_at', [$currentWeekStart, $currentWeekEnd])->count(),
                'month' => self::where('stars', 2)->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])->count(),
                'year' => self::where('stars', 2)->whereBetween('created_at', [$currentYearStart, $currentYearEnd])->count(),

            ],
            'three' => [
                'total' => self::where('stars', 3)->count(),
                'day' => self::where('stars', 3)->whereBetween('created_at', [$todayStart, $todayEnd])->count(),
                'week' => self::where('stars', 3)->whereBetween('created_at', [$currentWeekStart, $currentWeekEnd])->count(),
                'month' => self::where('stars', 3)->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])->count(),
                'year' => self::where('stars', 3)->whereBetween('created_at', [$currentYearStart, $currentYearEnd])->count(),

            ],
            'four' => [
                'total' => self::where('stars', 4)->count(),
                'day' => self::where('stars', 4)->whereBetween('created_at', [$todayStart, $todayEnd])->count(),
                'week' => self::where('stars', 4)->whereBetween('created_at', [$currentWeekStart, $currentWeekEnd])->count(),
                'month' => self::where('stars', 4)->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])->count(),
                'year' => self::where('stars', 4)->whereBetween('created_at', [$currentYearStart, $currentYearEnd])->count(),

            ],
            'five' => [
                'total' => self::where('stars', 5)->count(),
                'day' => self::where('stars', 5)->whereBetween('created_at', [$todayStart, $todayEnd])->count(),
                'week' => self::where('stars', 5)->whereBetween('created_at', [$currentWeekStart, $currentWeekEnd])->count(),
                'month' => self::where('stars', 5)->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])->count(),
                'year' => self::where('stars', 5)->whereBetween('created_at', [$currentYearStart, $currentYearEnd])->count(),

            ],
        ];
    }
}
