<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;

class AircraftType extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $table = 'aircraft_types';

    protected $guarded = [];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Aircraft Type')
            ->logAll()
            ->logExcept(['created_at', 'updated_at', 'deleted_at'])
            ->dontLogIfAttributesChangedOnly(['updated_at'])
            ->setDescriptionForEvent(fn(string $eventName) => "Aircraft Type {$eventName}")
            ->logOnlyDirty(true)
            ->dontSubmitEmptyLogs();
    }

    public function getFormattedIdAttribute()
    {
        return '#' . Str::padLeft($this->attributes['id'], 6, '0');
    }

    public function getFormattedNameAttribute()
    {
        return $this?->aircraftManufacturer?->name . ' ' . $this->attributes['name'];
    }

    /**
     * Scope a query to only include active aircrafts.
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('active', 1);
    }

    public function aircraftManufacturer()
    {
        return $this->belongsTo(AircraftManufacturer::class, 'aircraft_manufacturer_id');
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
