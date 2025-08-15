<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\ScheduleFlightCustomerShipment;
use Illuminate\Support\Facades\Auth;

class ScheduleFlightCustomerShipmentObserver
{
    /**
     * Handle the ScheduleFlightCustomerShipment "created" event.
     */
    public function created(ScheduleFlightCustomerShipment $scheduleFlightCustomerShipment): void
    {
        $customer = $scheduleFlightCustomerShipment->scheduleFlightCustomer->customer;
        $product = $scheduleFlightCustomerShipment->product;
        $destination = $scheduleFlightCustomerShipment->toAirport->name ?? 'NA';

        AuditLog::create([
            'user_id'      => auth()->id(),
            'action'       => 'created',
            'model_type'   => ScheduleFlightCustomerShipment::class,
            'model_id'     => $scheduleFlightCustomerShipment->id,
            'field_name'   => 'ALL',
            'old_value'    => null,
            'new_value'    => json_encode($scheduleFlightCustomerShipment->getAttributes()), // Optional: all fields
            'description'  => "For Customer {$customer->name} & Product {$product->name} : AWB {$scheduleFlightCustomerShipment->awb} added with Declared Weight {$scheduleFlightCustomerShipment->declared_weight}, Actual Weight {$scheduleFlightCustomerShipment->actual_weight}, Volumetric Weight {$scheduleFlightCustomerShipment->volumetric_weight}, Uplifted Weight {$scheduleFlightCustomerShipment->uplifted_weight}, Offloaded Weight {$scheduleFlightCustomerShipment->offloaded_weight}, Total Volumetric Weight {$scheduleFlightCustomerShipment->total_volumetric_weight}, Total Actual Weight {$scheduleFlightCustomerShipment->total_actual_weight} and Destination {$destination}",
            'performed_at' => now(),
        ]);
    }

    /**
     * Handle the ScheduleFlightCustomerShipment "updated" event.
     */
    public function updated(ScheduleFlightCustomerShipment $scheduleFlightCustomerShipment): void
    {
        $userId = Auth::id();
        $customerName = $scheduleFlightCustomerShipment->scheduleFlightCustomer->customer->name ?? 'NA';
        $productName = $scheduleFlightCustomerShipment->product->name ?? 'NA';

        foreach ($scheduleFlightCustomerShipment->getChanges() as $field => $newValue) {
            $oldValue = $scheduleFlightCustomerShipment->getOriginal($field);

            if ($field === 'updated_at' || $oldValue == $newValue) continue;

            if ($field === 'declared_weight') {
                $fieldName = 'Declared Weight';
            } else if ($field === 'actual_weight') {
                $fieldName = 'Actual Weight';
            } else if ($field === 'volumetric_weight') {
                $fieldName = 'Volumetric Weight';
            } else if ($field === 'uplifted_weight') {
                $fieldName = 'Uplifted Weight';
            } else if ($field === 'offloaded_weight') {
                $fieldName = 'Offloaded Weight';
            } else if ($field === 'total_volumetric_weight') {
                $fieldName = 'Total Volumetric Weight';
            } else if ($field === 'total_actual_weight') {
                $fieldName = 'Total Actual Weight';
            } else if ($field === 'destination') {
                $fieldName = 'Destination';
            } else {
                $fieldName = $field;
            }

            AuditLog::create([
                'user_id'     => $userId,
                'action'      => 'updated',
                'model_type'  => ScheduleFlightCustomerShipment::class,
                'model_id'    => $scheduleFlightCustomerShipment->id,
                'field_name'  => $field,
                'old_value'   => $oldValue,
                'new_value'   => $newValue,
                'description' => "For Customer $customerName & Product $productName : $fieldName changed from $oldValue to $newValue.",
                'performed_at' => now(),
            ]);
        }
    }

    /**
     * Handle the ScheduleFlightCustomerShipment "deleted" event.
     */
    public function deleted(ScheduleFlightCustomerShipment $scheduleFlightCustomerShipment): void
    {
        $customer = $scheduleFlightCustomerShipment->scheduleFlightCustomer->customer;
        $product = $scheduleFlightCustomerShipment->product;

        AuditLog::create([
            'user_id'      => auth()->id(),
            'action'       => 'deleted',
            'model_type'   => ScheduleFlightCustomerShipment::class,
            'model_id'     => $scheduleFlightCustomerShipment->id,
            'field_name'   => 'ALL',
            'old_value'    => json_encode($scheduleFlightCustomerShipment->getOriginal()), // Capture full record before deletion
            'new_value'    => null,
            'description'  => "AWB {$scheduleFlightCustomerShipment->awb} deleted for Customer {$customer->name} & Product {$product->name}",
            'performed_at' => now(),
        ]);
    }

    /**
     * Handle the ScheduleFlightCustomerShipment "restored" event.
     */
    public function restored(ScheduleFlightCustomerShipment $scheduleFlightCustomerShipment): void
    {
        //
    }

    /**
     * Handle the ScheduleFlightCustomerShipment "force deleted" event.
     */
    public function forceDeleted(ScheduleFlightCustomerShipment $scheduleFlightCustomerShipment): void
    {
        //
    }
}
