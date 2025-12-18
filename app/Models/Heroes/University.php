<?php

namespace App\Models\Heroes;

use App\Models\Volunteer\Faculty;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class University extends Model
{
    use LogsActivity;
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logUnguarded();
    }

    protected $table = 'universities';

    public function faculties()
    {
        return $this->hasMany(Faculty::class)->with('heroes');
    }

    public function fakultas()
    {
        return $this->hasMany(Faculty::class);
    }

    public function heroes()
    {
        return $this->hasManyThrough(Hero::class, Faculty::class);
    }

    public function foods()
    {
        $foods = $this->heroes->sum('weight');

        return $foods;
    }
}
