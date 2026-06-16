<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Carbon\Carbon;
use App\Models\Note;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'role',
        'status',
        'avatar',
        'password',
        'email'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    public function receivesBroadcastNotificationsOn(): string
    {
        return 'users.' . $this->id;
    }

    public function getTodaysNotificationsCount()
    {
        return $this->notifications()->whereBetween('created_at', [Carbon::today()->startOfDay(), Carbon::today()->endOfDay()])
            ->count();
    }

    public function notes()
    {
        return $this->hasMany(Note::class);
    }

    public function activities()
    {
        return $this->hasMany(Activity::class, 'causer_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(["*"]);
    }



    public function tapActivity(Activity $activity, string $eventName)
    {
        $causer = auth()->user();
        if (!$causer || $causer->id === $this->id || $causer->role !== 'admin') {
            activity()->disableLogging();
            return;
        }

        if ($eventName === 'created') {
            $activity->description = 'user.created';
            $activity->icon = 'ri-add-box-line';
        } elseif ($eventName === 'deleted') {
            $activity->description = 'user.deleted';
            $activity->icon = 'ri-delete-bin-line';
        } elseif ($eventName === 'updated') {
            $changes = $this->getChanges();
            $roleChanged = array_key_exists('role', $changes);
            $statusChanged = array_key_exists('status', $changes);
            $activity->icon = 'ri-edit-box-line';
            if ($roleChanged && $statusChanged) {
                if ($this->status === 'banned' && $this->role === 'admin') {
                    $activity->description = 'user.banned_and_assigned_as_admin';
                } elseif ($this->status === 'banned' && $this->role === 'moderator') {
                    $activity->description = 'user.banned_and_assigned_as_moderator';
                } elseif ($this->status === 'active' && $this->role === 'admin') {
                    $activity->description = 'user.activated_and_assigned_as_admin';
                } elseif ($this->status === 'active' && $this->role === 'moderator') {
                    $activity->description = 'user.activated_and_assigned_as_moderator';
                }
            } elseif ($roleChanged) {
                $activity->description = $this->role === 'admin'
                    ? 'user.assigned_as_admin'
                    : 'user.assigned_as_moderator';
            } elseif ($statusChanged) {
                $activity->description = $this->status === 'banned'
                    ? 'user.banned'
                    : 'user.activated';
            }
        }
    }
}
