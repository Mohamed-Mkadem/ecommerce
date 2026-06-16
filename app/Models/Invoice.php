<?php

namespace App\Models;

use Carbon\Carbon;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class Invoice extends Model
{

    use LogsActivity;
    protected $fillable = [
        'type',
        'title',
        'category',
        'description',
        'amount',
        'invoiceable_id',
        'invoiceable_type',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(["*"]);
    }

    public function tapActivity(Activity $activity, string $eventName)
    {
        if ($eventName === 'created') {
            $activity->description = 'invoice.created';
            $activity->icon = 'ri-add-box-line';
        } elseif ($eventName === 'updated') {
            $activity->description = 'invoice.updated';
            $activity->icon = 'ri-edit-box-line';
        } elseif ($eventName === 'deleted') {
            $activity->description = 'invoice.deleted';
            $activity->icon = 'ri-delete-bin-line';
        }
    }


    public function invoiceable()
    {
        return $this->morphTo();
    }
    public static function getExpenses($inDT = false)
    {
        $todayStart = Carbon::today()->startOfDay();
        $todayEnd = Carbon::today()->endOfDay();

        $currentWeekStart = Carbon::now()->startOfWeek();
        $currentWeekEnd = Carbon::now()->endOfWeek();

        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();

        $currentYearStart = Carbon::now()->startOfYear();
        $currentYearEnd = Carbon::now()->endOfYear();



        $totalExpenses = self::where('type', 'expense')->sum('amount');
        $todayExpenses = self::where('type', 'expense')->whereBetween('created_at', [$todayStart, $todayEnd])
            ->sum('amount');

        $weekExpenses = self::where('type', 'expense')->whereBetween('created_at', [$currentWeekStart, $currentWeekEnd])->sum('amount');

        $monthExpenses = self::where('type', 'expense')->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])
            ->sum('amount');
        $yearExpenses = self::where('type', 'expense')->whereBetween('created_at', [$currentYearStart, $currentYearEnd])
            ->sum('amount');

        return [
            'total' => $inDT ? self::formatAmount($totalExpenses) : $totalExpenses,
            'day' => $inDT ? self::formatAmount($todayExpenses) : $todayExpenses,
            'week' => $inDT ? self::formatAmount($weekExpenses) : $weekExpenses,
            'month' => $inDT ? self::formatAmount($monthExpenses) : $monthExpenses,
            'year' => $inDT ? self::formatAmount($yearExpenses) : $yearExpenses,

        ];
    }
    public static function getRevenues($inDT = false)
    {
        $todayStart = Carbon::today()->startOfDay();
        $todayEnd = Carbon::today()->endOfDay();

        $currentWeekStart = Carbon::now()->startOfWeek();
        $currentWeekEnd = Carbon::now()->endOfWeek();

        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();

        $currentYearStart = Carbon::now()->startOfYear();
        $currentYearEnd = Carbon::now()->endOfYear();

        $totalRevenues = self::where('type', 'revenue')->sum('amount');
        $todayRevenues = self::where('type', 'revenue')->whereBetween('created_at', [$todayStart, $todayEnd])
            ->sum('amount');

        $weekRevenues = self::where('type', 'revenue')->whereBetween('created_at', [$currentWeekStart, $currentWeekEnd])->sum('amount');

        $monthRevenues = self::where('type', 'revenue')->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])
            ->sum('amount');
        $yearRevenues = self::where('type', 'revenue')->whereBetween('created_at', [$currentYearStart, $currentYearEnd])
            ->sum('amount');

        return [
            'total' => $inDT ? self::formatAmount($totalRevenues) : $totalRevenues,
            'day' => $inDT ? self::formatAmount($todayRevenues) : $todayRevenues,
            'week' => $inDT ? self::formatAmount($weekRevenues) : $weekRevenues,
            'month' => $inDT ? self::formatAmount($monthRevenues) : $monthRevenues,
            'year' => $inDT ? self::formatAmount($yearRevenues) : $yearRevenues,

        ];
    }
    public static function getEarnings($inDT = false)
    {

        $totalEarnings = self::getRevenues()['total'] - self::getExpenses()['total'];
        $todayEarnings = self::getRevenues()['day'] - self::getExpenses()['day'];
        $weekEarnings = self::getRevenues()['week'] - self::getExpenses()['week'];
        $monthEarnings = self::getRevenues()['month'] - self::getExpenses()['month'];
        $yearEarnings = self::getRevenues()['year'] - self::getExpenses()['year'];


        return [
            'total' => $inDT ? self::formatAmount($totalEarnings) : $totalEarnings,
            'day' => $inDT ? self::formatAmount($todayEarnings) : $todayEarnings,
            'week' => $inDT ? self::formatAmount($weekEarnings) : $weekEarnings,
            'month' => $inDT ? self::formatAmount($monthEarnings) : $monthEarnings,
            'year' => $inDT ? self::formatAmount($yearEarnings) : $yearEarnings,

        ];
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
        ];
    }



    public static function getRevenuesCategories()
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
            'total' => self::getCategoryCountsAndAmounts('revenue'),
            'day' => self::getCategoryCountsAndAmounts('revenue', $todayStart, $todayEnd),
            'week' => self::getCategoryCountsAndAmounts('revenue', $currentWeekStart, $currentWeekEnd),
            'month' => self::getCategoryCountsAndAmounts('revenue', $currentMonthStart, $currentMonthEnd),
            'year' => self::getCategoryCountsAndAmounts('revenue', $currentYearStart, $currentYearEnd),
        ];
    }
    public static function getExpensesCategories()
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
            'total' => self::getCategoryCountsAndAmounts('expense'),
            'day' => self::getCategoryCountsAndAmounts('expense', $todayStart, $todayEnd),
            'week' => self::getCategoryCountsAndAmounts('expense', $currentWeekStart, $currentWeekEnd),
            'month' => self::getCategoryCountsAndAmounts('expense', $currentMonthStart, $currentMonthEnd),
            'year' => self::getCategoryCountsAndAmounts('expense', $currentYearStart, $currentYearEnd),
        ];
    }

    private static function getCategoryCountsAndAmounts($type, $startDate = null, $endDate = null)
    {
        $categories = ['marketing', 'operating costs', 'other', 'payments', 'daily sales'];

        $query = self::where('type', $type)
            ->groupBy('category')
            ->selectRaw('category, COUNT(*) as count, SUM(amount) as amount');

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        $results = $query->get()->keyBy('category')->toArray();

        $initializedResults = [];
        foreach ($categories as $category) {
            $initializedResults[$category] = [
                'category' => $category,
                'count' => 0,
                'amount' => self::formatAmount(0),
            ];
        }

        foreach ($results as $category => $result) {
            $initializedResults[$category] = [
                'category' => $category,
                'count' => $result['count'],
                'amount' => self::formatAmount($result['amount']),
            ];
        }

        $sortedResults = collect($initializedResults)->sortByDesc('amount')->toArray();

        return $sortedResults;
    }


    private static function formatAmount($amount)
    {
        return number_format($amount / 1000, 3, '.', '');
    }
}
