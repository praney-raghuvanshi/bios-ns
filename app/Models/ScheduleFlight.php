<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;

class ScheduleFlight extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    const STATUS_OPEN = 1;
    const STATUS_COMPLETED = 2;
    const STATUS_CANCELLED = 3;
    const STATUS_VALIDATED = 4;

    protected $guarded = [];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Schedule Flight')
            ->logAll()
            ->logExcept(['created_at', 'updated_at', 'deleted_at'])
            ->dontLogIfAttributesChangedOnly(['updated_at'])
            ->setDescriptionForEvent(fn(string $eventName) => "Schedule Flight {$eventName}")
            ->logOnlyDirty(true)
            ->dontSubmitEmptyLogs();
    }

    public static function getStatusOptions()
    {
        return [
            self::STATUS_OPEN => 'Open',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_CANCELLED => 'Cancelled',
            self::STATUS_VALIDATED => 'Validated'
        ];
    }

    public function getStatusLabel()
    {
        return self::getStatusOptions()[$this->status] ?? 'Unknown';
    }

    public function getFormattedIdAttribute()
    {
        return '#' . Str::padLeft($this->attributes['id'], 6, '0');
    }

    public function getFormattedEtdAttribute()
    {
        return $this->estimated_departure_time
            ? \Carbon\Carbon::parse($this->estimated_departure_time)->format('H:i')
            : '--';
    }

    public function getFormattedAtdAttribute()
    {
        return $this->actual_departure_time
            ? \Carbon\Carbon::parse($this->actual_departure_time)->format('H:i')
            : '--';
    }

    public function getFormattedEtaAttribute()
    {
        return $this->estimated_arrival_time
            ? \Carbon\Carbon::parse($this->estimated_arrival_time)->format('H:i')
            : '--';
    }

    public function getFormattedAtaAttribute()
    {
        return $this->actual_arrival_time
            ? \Carbon\Carbon::parse($this->actual_arrival_time)->format('H:i')
            : '--';
    }

    public function getFormattedDepartureTimeDiffAttribute()
    {
        if (is_null($this->departure_time_diff)) {
            return '--';
        }
        return Carbon::createFromTime(0, 0, 0)->addMinutes(abs($this->departure_time_diff))->format('H:i');
    }

    public function getFormattedArrivalTimeDiffAttribute()
    {
        if (is_null($this->arrival_time_diff)) {
            return '--';
        }
        return Carbon::createFromTime(0, 0, 0)->addMinutes(abs($this->arrival_time_diff))->format('H:i');
    }

    public function getEtdLocalAttribute()
    {
        if (is_null($this->attributes['estimated_departure_time'])) {
            return null;
        }
        $EtdUtc = Carbon::parse($this->attributes['estimated_departure_time'])->tz('UTC');
        $airportTimezone = $this->flight->fromAirport->timezone;
        $EtdLocal = $EtdUtc->copy()->tz($airportTimezone);
        return $EtdLocal->format('H:i');
    }

    public function getAtdLocalAttribute()
    {
        if (is_null($this->attributes['actual_departure_time'])) {
            return null;
        }
        $AtdUtc = Carbon::parse($this->attributes['actual_departure_time'])->tz('UTC');
        $airportTimezone = $this->flight->fromAirport->timezone;
        $AtdLocal = $AtdUtc->copy()->tz($airportTimezone);
        return $AtdLocal->format('H:i');
    }

    public function getEtaLocalAttribute()
    {
        if (is_null($this->attributes['estimated_arrival_time'])) {
            return null;
        }
        $EtaUtc = Carbon::parse($this->attributes['estimated_arrival_time'])->tz('UTC');
        $airportTimezone = $this->flight->toAirport->timezone;
        $EtaLocal = $EtaUtc->copy()->tz($airportTimezone);
        return $EtaLocal->format('H:i');
    }

    public function getAtaLocalAttribute()
    {
        if (is_null($this->attributes['actual_arrival_time'])) {
            return null;
        }
        $AtaUtc = Carbon::parse($this->attributes['actual_arrival_time'])->tz('UTC');
        $airportTimezone = $this->flight->toAirport->timezone;
        $AtaLocal = $AtaUtc->copy()->tz($airportTimezone);
        return $AtaLocal->format('H:i');
    }

    /**
     * Scope a query to only include active schedule flights.
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('active', 1);
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class, 'schedule_id');
    }

    public function flight()
    {
        return $this->belongsTo(Flight::class, 'flight_id');
    }

    public function aircraft()
    {
        return $this->belongsTo(Aircraft::class, 'aircraft_id');
    }

    public function scheduleFlightCustomers()
    {
        return $this->hasMany(ScheduleFlightCustomer::class, 'schedule_flight_id', 'id');
    }

    public function customers()
    {
        return $this->belongsToMany(Customer::class, 'schedule_flight_customers');
    }

    public function scheduleFlightRemarks()
    {
        return $this->hasMany(ScheduleFlightRemark::class, 'schedule_flight_id', 'id');
    }

    public function scheduleFlightEmails()
    {
        return $this->hasMany(ScheduleFlightEmail::class, 'schedule_flight_id', 'id');
    }

    public function auditLogs()
    {
        return $this->morphMany(AuditLog::class, 'model');
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
