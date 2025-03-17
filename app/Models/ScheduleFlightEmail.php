<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;

class ScheduleFlightEmail extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function getFormattedIdAttribute()
    {
        return '#' . Str::padLeft($this->attributes['id'], 6, '0');
    }

    /**
     * Scope a query to only include active schedule flight emails.
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('active', 1);
    }

    public function scheduleFlight()
    {
        return $this->belongsTo(ScheduleFlight::class, 'schedule_flight_id', 'id');
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
