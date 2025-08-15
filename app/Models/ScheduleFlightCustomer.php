<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;

class ScheduleFlightCustomer extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $guarded = [];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Schedule Flight Customer')
            ->logAll()
            ->logExcept(['created_at', 'updated_at', 'deleted_at'])
            ->dontLogIfAttributesChangedOnly(['updated_at'])
            ->setDescriptionForEvent(fn(string $eventName) => "Schedule Flight Customer {$eventName}")
            ->logOnlyDirty(true)
            ->dontSubmitEmptyLogs();
    }

    public function getFormattedIdAttribute()
    {
        return '#' . Str::padLeft($this->attributes['id'], 6, '0');
    }

    /**
     * Scope a query to only include active schedule flight customer.
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('active', 1);
    }

    public function productAndAwbData()
    {
        $results = $awbTotals = [];

        // Get all shipments linked to this schedule flight customer
        foreach ($this->scheduleFlightCustomerShipments as $shipment) {
            $product = $shipment->product; // Access the related product

            if (!$product) continue; // Skip if product not found

            $productCode = $product->code;
            $weight = $shipment->volumetric_weight > 0
                ? $shipment->volumetric_weight
                : $shipment->actual_weight;

            // Sum per product code
            if (!isset($awbTotals[$productCode])) {
                $awbTotals[$productCode] = 0;
            }
            $awbTotals[$productCode] += $weight;
        }

        $dbTotals = ScheduleFlightCustomerProduct::where('schedule_flight_customer_id', $this->id)
            ->with('product')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->product->code => (int) $item->uplifted_weight];
            })
            ->toArray();

        $allProductCodes = array_unique(array_merge(array_keys($awbTotals), array_keys($dbTotals)));

        foreach ($allProductCodes as $productCode) {
            $awbTotal = $awbTotals[$productCode] ?? 0;
            $dbTotal = $dbTotals[$productCode] ?? 0;

            $isEqual = ($awbTotal === $dbTotal);

            $results[] = [
                'is_equal' => $isEqual,
                'msg' => "Customer $productCode Total: $dbTotal - AWB $productCode Total: $awbTotal"
            ];
        }

        return $results;
    }


    public function scheduleFlightCustomerProducts()
    {
        return $this->hasMany(ScheduleFlightCustomerProduct::class, 'schedule_flight_customer_id', 'id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'schedule_flight_customer_products');
    }

    public function scheduleFlightCustomerShipments()
    {
        return $this->hasMany(ScheduleFlightCustomerShipment::class, 'schedule_flight_customer_id', 'id');
    }

    public function scheduleFlight()
    {
        return $this->belongsTo(ScheduleFlight::class, 'schedule_flight_id', 'id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
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
