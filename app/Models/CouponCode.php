<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Order;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;

class CouponCode extends Model
{
    use SoftDeletes, LogsActivity;

    protected $table = 'coupon_codes';

    protected $fillable = ['code', 'status', 'value'];

    public function orders()
    {
        return $this->hasMany(Order::class);
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
            'total' => self::count(),
            'day' => self::whereBetween('created_at', [$todayStart, $todayEnd])->count(),
            'week' => self::whereBetween('created_at', [$currentWeekStart, $currentWeekEnd])->count(),
            'month' => self::whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])->count(),
            'year' => self::whereBetween('created_at', [$currentYearStart, $currentYearEnd])->count(),
            'inactive' => self::where('status', 'inactive')->count(),
            'active' => self::where('status', 'active')->count(),

        ];
    }
    public static function statistics()
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
            'total' => self::count(),
            'day' => self::whereBetween('created_at', [$todayStart, $todayEnd])->count(),
            'week' => self::whereBetween('created_at', [$currentWeekStart, $currentWeekEnd])->count(),
            'month' => self::whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])->count(),
            'year' => self::whereBetween('created_at', [$currentYearStart, $currentYearEnd])->count(),
        ];
    }


    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(["*"]);
    }

    public function tapActivity(Activity $activity, string $eventName)
    {
        if ($eventName === 'created') {
            $activity->description = 'couponCode.created';
            $activity->icon = 'ri-add-box-line';
        } elseif ($eventName === 'updated') {
            if ($this->status === 'inactive') {
                $activity->description = 'couponCode.inactivated';
            } elseif ($this->status === 'active') {
                $activity->description = 'couponCode.activated';
            }

            $activity->icon = 'ri-edit-box-line';
        } elseif ($eventName === 'deleted') {
            $activity->description = 'couponCode.deleted';
            $activity->icon = 'ri-delete-bin-line';
        }
    }
}
