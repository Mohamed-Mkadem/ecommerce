<?php

namespace App\Models;

use App\Models\Order;
use App\Models\ShippingReport;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;

class Shipper extends Model
{
    /** @use HasFactory<\Database\Factories\ShipperFactory> */
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = ['name'];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function shippingReports()
    {
        return $this->hasMany(ShippingReport::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(["*"]);
    }

    public function tapActivity(Activity $activity, string $eventName)
    {
        if ($eventName === 'created') {
            $activity->description = 'shipper.created';
            $activity->icon = 'ri-add-box-line';
        } elseif ($eventName === 'updated') {
            $activity->description = 'shipper.updated';
            $activity->icon = 'ri-edit-box-line';
        } elseif ($eventName === 'deleted') {
            $activity->description = 'shipper.deleted';
            $activity->icon = 'ri-delete-bin-line';
        }
    }
}
