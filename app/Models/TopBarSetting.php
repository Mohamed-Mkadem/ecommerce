<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class TopBarSetting extends Model
{
    use LogsActivity;
    protected $fillable = [
        'is_visible',
        'text_content',
    ];

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
        $activity->description = 'topBarSettings.updated';
        $activity->icon = 'ri-edit-box-line';
    }
}
