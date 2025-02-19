<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Models\Role;

class Group extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $guarded = [];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Group')
            ->logAll()
            ->logExcept(['created_at', 'updated_at', 'deleted_at'])
            ->dontLogIfAttributesChangedOnly(['updated_at'])
            ->setDescriptionForEvent(fn(string $eventName) => "Group {$eventName}")
            ->logOnlyDirty(true)
            ->dontSubmitEmptyLogs();
    }

    public function getFormattedIdAttribute()
    {
        return '#' . Str::padLeft($this->attributes['id'], 6, '0');
    }

    /**
     * Scope a query to only include active groups.
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('active', 1);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'group_roles', 'group_id', 'role_id');
    }

    public function addedByUser()
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}
