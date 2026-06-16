<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\City;
use App\Models\Note;
use App\Models\Order;
use App\Models\Review;
use App\Models\Locality;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\LogOptions;
use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Client extends Model
{
    /** @use HasFactory<\Database\Factories\ClientFactory> */
    use HasFactory, SoftDeletes, LogsActivity;



    protected $fillable = ['name', 'state_id', 'city_id', 'locality_id', 'phone', 'phone2', 'address'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(["*"]);
    }

    public function tapActivity(Activity $activity, string $eventName)
    {
        if ($eventName === 'updated') {
            $activity->description = 'client.updated';
            $activity->icon = 'ri-edit-box-line';
        }
        if ($eventName === 'created') {
            $activity->description = 'client.created';
            $activity->icon = 'ri-add-box-line';
        }
        if ($eventName === 'deleted') {
            $activity->description = 'client.deleted';
            $activity->icon = 'ri-delete-bin-line';
        }
    }

    public function orders()
    {
        return $this->hasMany(Order::class)->withTrashed();
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function notes()
    {
        return $this->morphMany(Note::class, 'notable');
    }

    protected function getCreatedAtAttribute($value)
    {
        return   Carbon::parse($value)->format('d-m-Y');
    }
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }
    public function locality()
    {
        return $this->belongsTo(Locality::class);
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

    public static function getClientsByStates()
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
            ->rightJoin('states', 'clients.state_id', '=', 'states.id')
            ->join('state_translations', function ($join) use ($locale) {
                $join->on('states.id', '=', 'state_translations.state_id')
                    ->where('state_translations.locale', '=', $locale);
            })
            ->groupBy('state_translations.name')
            ->selectRaw('state_translations.name as state, COALESCE(COUNT(clients.id), 0) as count');

        if ($startDate && $endDate) {
            $query->whereBetween('clients.created_at', [$startDate, $endDate]);
        }

        $results = $query->get()->keyBy('state')->toArray();


        foreach ($results as $state => $result) {
            $initializedResults[$state]['count'] = $result['count'];
        }


        $sortedResults = collect($initializedResults)->sortByDesc('count')->toArray();

        return $sortedResults;
    }
}
