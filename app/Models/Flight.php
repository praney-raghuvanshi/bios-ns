<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Flight extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $guarded = [];

    protected $appends = [
        'formatted_id',
        'departure_time_local',
        'arrival_time_local'
    ];

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

    public function getDepartureTimeLocalAttribute()
    {
        $departureTimeUtc = Carbon::parse($this->attributes['departure_time'])->tz('UTC');
        $airportTimezone = $this->fromAirport->timezone;
        $departureTimeLocal = $departureTimeUtc->copy()->tz($airportTimezone);
        return $departureTimeLocal->format('H:i');
    }

    public function getArrivalTimeLocalAttribute()
    {
        $arrivalTimeUtc = Carbon::parse($this->attributes['arrival_time'])->tz('UTC');
        $airportTimezone = $this->toAirport->timezone;
        $arrivalTimeLocal = $arrivalTimeUtc->copy()->tz($airportTimezone);
        return $arrivalTimeLocal->format('H:i');
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

    public function aircraftType()
    {
        return $this->belongsTo(AircraftType::class, 'aircraft_type_id', 'id');
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
