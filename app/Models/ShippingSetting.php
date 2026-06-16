<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class ShippingSetting extends Model
{

    use LogsActivity;
    protected $fillable = [
        'tunis_acceptance_delivery_date',
        'wilayet_acceptance_delivery_date',
    ];

    protected $casts = [
        'tunis_acceptance_delivery_date' => 'date',
        'wilayet_acceptance_delivery_date' => 'date',
    ];
    public function getTunisAcceptanceDeliveryDateAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->format('Y-m-d');
    }
    public function getWilayetAcceptanceDeliveryDateAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->format('Y-m-d');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(["*"]);
    }

    public function tapActivity(Activity $activity, string $eventName)
    {
        if ($eventName !== 'updated') {
            activity()->disableLogging();
        }
        $activity->description = 'acceptance_dates.updated';
        $activity->icon = 'ri-edit-box-line';
    }
}
