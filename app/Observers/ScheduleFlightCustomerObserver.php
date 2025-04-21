<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\ScheduleFlightCustomer;
use Illuminate\Support\Facades\Auth;

class ScheduleFlightCustomerObserver
{
    /**
     * Handle the ScheduleFlightCustomer "created" event.
     */
    public function created(ScheduleFlightCustomer $scheduleFlightCustomer): void
    {
        $customer = $scheduleFlightCustomer->customer;
        AuditLog::create([
            'user_id'      => auth()->id(),
            'action'       => 'created',
            'model_type'   => ScheduleFlightCustomer::class,
            'model_id'     => $scheduleFlightCustomer->id,
            'field_name'   => 'ALL',
            'old_value'    => null,
            'new_value'    => json_encode($scheduleFlightCustomer->getAttributes()),
            'description'  => "Customer $customer->name linked to schedule flight.",
            'performed_at' => now(),
        ]);
    }

    /**
     * Handle the ScheduleFlightCustomer "updated" event.
     */
    public function updated(ScheduleFlightCustomer $scheduleFlightCustomer): void
    {
        $userId = Auth::id();
        $customerName = $scheduleFlightCustomer->customer->name ?? 'NA';

        foreach ($scheduleFlightCustomer->getChanges() as $field => $newValue) {
            $oldValue = $scheduleFlightCustomer->getOriginal($field);

            if ($field === 'updated_at' || $oldValue == $newValue) continue;

            if ($field === 'total_uplifted_weight') {
                $fieldName = 'Total Uplifted Weight';
            } else if ($field === 'total_offloaded_weight') {
                $fieldName = 'Total Offloaded Weight';
            }

            AuditLog::create([
                'user_id'     => $userId,
                'action'      => 'updated',
                'model_type'  => ScheduleFlightCustomer::class,
                'model_id'    => $scheduleFlightCustomer->id,
                'field_name'  => $field,
                'old_value'   => $oldValue,
                'new_value'   => $newValue,
                'description' => "For Customer $customerName : $fieldName changed from $oldValue to $newValue.",
                'performed_at' => now(),
            ]);
        }
    }

    /**
     * Handle the ScheduleFlightCustomer "deleted" event.
     */
    public function deleted(ScheduleFlightCustomer $scheduleFlightCustomer): void
    {
        $customer = $scheduleFlightCustomer->customer;
        AuditLog::create([
            'user_id'      => auth()->id(),
            'action'       => 'deleted',
            'model_type'   => ScheduleFlightCustomer::class,
            'model_id'     => $scheduleFlightCustomer->id,
            'field_name'   => 'ALL',
            'old_value'    => json_encode($scheduleFlightCustomer->getOriginal()),
            'new_value'    => null,
            'description'  => "Customer $customer->name unlinked from schedule flight.",
            'performed_at' => now(),
        ]);
    }

    /**
     * Handle the ScheduleFlightCustomer "restored" event.
     */
    public function restored(ScheduleFlightCustomer $scheduleFlightCustomer): void
    {
        //
    }

    /**
     * Handle the ScheduleFlightCustomer "force deleted" event.
     */
    public function forceDeleted(ScheduleFlightCustomer $scheduleFlightCustomer): void
    {
        //
    }
}
