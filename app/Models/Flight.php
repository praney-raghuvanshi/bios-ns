<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;

class Flight extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $guarded = [];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Flight')
            ->logAll()
            ->logExcept(['created_at', 'updated_at', 'deleted_at'])
            ->dontLogIfAttributesChangedOnly(['updated_at'])
            ->setDescriptionForEvent(fn(string $eventName) => "Flight {$eventName}")
            ->logOnlyDirty(true)
            ->dontSubmitEmptyLogs();
    }

    public function getFormattedIdAttribute()
    {
        return '#' . Str::padLeft($this->attributes['id'], 6, '0');
    }

    /**
     * Scope a query to only include active flights.
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('active', 1);
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id', 'id');
    }

    public function fromAirport()
    {
        return $this->belongsTo(Airport::class, 'from', 'id');
    }

    public function toAirport()
    {
        return $this->belongsTo(Airport::class, 'to', 'id');
    }

    public function aircraft()
    {
        return $this->belongsTo(Aircraft::class, 'aircraft_id', 'id');
    }

    public function correspondingFlight()
    {
        return $this->belongsTo(Flight::class, 'corresponding_flight', 'id');
    }

    public function clonedFrom()
    {
        return $this->belongsTo(Flight::class, 'cloned_from', 'id');
    }

    public function flightDays()
    {
        return $this->hasMany(FlightDay::class, 'flight_id', 'id');
    }

    public function schedules()
    {
        return $this->belongsToMany(Schedule::class, 'schedule_flights');
    }

    public function addedByUser()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function deletedByUser()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
