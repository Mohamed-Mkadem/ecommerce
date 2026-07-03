<?php

namespace App\Models;

use App\Models\City;
use App\Models\Order;
use App\Models\Client;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;
use Astrotomic\Translatable\Translatable;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;

class State extends Model  implements TranslatableContract
{
    /** @use HasFactory<\Database\Factories\StateFactory> */
    use HasFactory, Translatable, LogsActivity;

    public $translatedAttributes = ['name'];

    protected $fillable = ['shipping_cost', 'delivery_cost', 'return_cost', 'default_shipper_id'];
    
    public function defaultShipper()
    {
        return $this->belongsTo(Shipper::class, 'default_shipper_id');
    }

    public function clients()
    {
        return $this->hasMany(Client::class);
    }
    public function orders()
    {
        return $this->hasMany(Order::class);
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
        $activity->description = 'state.updated';
        $activity->icon = 'ri-edit-box-line';
    }

    public function cities()
    {
        return $this->hasMany(City::class);
    }
}
