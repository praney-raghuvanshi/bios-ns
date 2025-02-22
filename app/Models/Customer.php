<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;

class Customer extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $guarded = [];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Customer')
            ->logAll()
            ->logExcept(['created_at', 'updated_at', 'deleted_at'])
            ->dontLogIfAttributesChangedOnly(['updated_at'])
            ->setDescriptionForEvent(fn(string $eventName) => "Customer {$eventName}")
            ->logOnlyDirty(true)
            ->dontSubmitEmptyLogs();
    }

    public function getFormattedIdAttribute()
    {
        return '#' . Str::padLeft($this->attributes['id'], 6, '0');
    }

    /**
     * Scope a query to only include active customers.
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('active', 1);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'customer_product', 'customer_id', 'product_id');
    }

    public function emails()
    {
        return $this->hasMany(CustomerEmail::class, 'customer_id');
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
