<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\ScheduleFlightCustomerProduct;
use Illuminate\Support\Facades\Auth;

class ScheduleFlightCustomerProductObserver
{
    /**
     * Handle the ScheduleFlightCustomerProduct "created" event.
     */
    public function created(ScheduleFlightCustomerProduct $scheduleFlightCustomerProduct): void
    {
        $customer = $scheduleFlightCustomerProduct->scheduleFlightCustomer->customer;
        $product = $scheduleFlightCustomerProduct->product;

        AuditLog::create([
            'user_id'      => auth()->id(),
            'action'       => 'created',
            'model_type'   => ScheduleFlightCustomerProduct::class,
            'model_id'     => $scheduleFlightCustomerProduct->id,
            'field_name'   => 'ALL',
            'old_value'    => null,
            'new_value'    => json_encode($scheduleFlightCustomerProduct->getAttributes()),
            'description'  => "Product $product->name linked to Customer $customer->name with Uplifted Weight {$scheduleFlightCustomerProduct->uplifted_weight} and Offloaded Weight {$scheduleFlightCustomerProduct->offloaded_weight}.",
            'performed_at' => now(),
        ]);
    }

    /**
     * Handle the ScheduleFlightCustomerProduct "updated" event.
     */
    public function updated(ScheduleFlightCustomerProduct $scheduleFlightCustomerProduct): void
    {
        $userId = Auth::id();
        $customerName = $scheduleFlightCustomerProduct->scheduleFlightCustomer->customer->name ?? 'NA';
        $productName = $scheduleFlightCustomerProduct->product->name ?? 'NA';

        foreach ($scheduleFlightCustomerProduct->getChanges() as $field => $newValue) {
            $oldValue = $scheduleFlightCustomerProduct->getOriginal($field);

            if ($field === 'updated_at' || $oldValue == $newValue) continue;

            if ($field === 'uplifted_weight') {
                $fieldName = 'Uplifted Weight';
            } else if ($field === 'offloaded_weight') {
                $fieldName = 'Offloaded Weight';
            }

            AuditLog::create([
                'user_id'     => $userId,
                'action'      => 'updated',
                'model_type'  => ScheduleFlightCustomerProduct::class,
                'model_id'    => $scheduleFlightCustomerProduct->id,
                'field_name'  => $field,
                'old_value'   => $scheduleFlightCustomerProduct->getOriginal($field),
                'new_value'   => $newValue,
                'description' => "For Customer $customerName & Product $productName : $fieldName changed from $oldValue to $newValue.",
                'performed_at' => now(),
            ]);
        }
    }

    /**
     * Handle the ScheduleFlightCustomerProduct "deleted" event.
     */
    public function deleted(ScheduleFlightCustomerProduct $scheduleFlightCustomerProduct): void
    {
        $customer = $scheduleFlightCustomerProduct->scheduleFlightCustomer->customer;
        $product = $scheduleFlightCustomerProduct->product;

        AuditLog::create([
            'user_id'      => auth()->id(),
            'action'       => 'deleted',
            'model_type'   => ScheduleFlightCustomerProduct::class,
            'model_id'     => $scheduleFlightCustomerProduct->id,
            'field_name'   => 'ALL',
            'old_value'    => json_encode($scheduleFlightCustomerProduct->getOriginal()), // Capture full record before deletion
            'new_value'    => null,
            'description'  => "Product $product->name unlinked from Customer $customer->name.",
            'performed_at' => now(),
        ]);
    }

    /**
     * Handle the ScheduleFlightCustomerProduct "restored" event.
     */
    public function restored(ScheduleFlightCustomerProduct $scheduleFlightCustomerProduct): void
    {
        //
    }

    /**
     * Handle the ScheduleFlightCustomerProduct "force deleted" event.
     */
    public function forceDeleted(ScheduleFlightCustomerProduct $scheduleFlightCustomerProduct): void
    {
        //
    }
}
