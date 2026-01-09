<?php

namespace App\Models\Volunteer;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class User extends Authenticatable
{
    use HasFactory, Notifiable, LogsActivity;
    protected $guarded = [];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['role', 'division_id', 'name', 'point']);
    }

    protected $hidden = [
        'password',
    ];
    public function activities(): HasMany
    {
        return $this->hasMany(\Spatie\Activitylog\Models\Activity::class, 'causer_id');
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
    public function reimburses()
    {
        return $this->hasMany(Reimburse::class);
    }
    public function cancelation()
    {
        return $this->hasOne(Cancelation::class);
    }

    public function points()
    {
        return $this->attendances->sum('point');
    }
}
