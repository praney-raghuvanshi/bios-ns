<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\ScheduleFlightRemark;
use Illuminate\Support\Facades\Auth;

class ScheduleFlightRemarkObserver
{
    /**
     * Handle the ScheduleFlightRemark "created" event.
     */
    public function created(ScheduleFlightRemark $scheduleFlightRemark): void
    {
        AuditLog::create([
            'user_id'      => auth()->id(),
            'action'       => 'created',
            'model_type'   => ScheduleFlightRemark::class,
            'model_id'     => $scheduleFlightRemark->id,
            'field_name'   => 'ALL',
            'old_value'    => null,
            'new_value'    => json_encode($scheduleFlightRemark->getAttributes()), // Optional: all fields
            'performed_at' => now(),
        ]);
    }

    /**
     * Handle the ScheduleFlightRemark "updated" event.
     */
    public function updated(ScheduleFlightRemark $scheduleFlightRemark): void
    {
        $userId = Auth::id();

        foreach ($scheduleFlightRemark->getChanges() as $field => $newValue) {
            if ($field === 'updated_at') continue;

            AuditLog::create([
                'user_id'     => $userId,
                'action'      => 'updated',
                'model_type'  => ScheduleFlightRemark::class,
                'model_id'    => $scheduleFlightRemark->id,
                'field_name'  => $field,
                'old_value'   => $scheduleFlightRemark->getOriginal($field),
                'new_value'   => $newValue,
                'performed_at' => now(),
            ]);
        }
    }

    /**
     * Handle the ScheduleFlightRemark "deleted" event.
     */
    public function deleted(ScheduleFlightRemark $scheduleFlightRemark): void
    {
        AuditLog::create([
            'user_id'      => auth()->id(),
            'action'       => 'deleted',
            'model_type'   => ScheduleFlightRemark::class,
            'model_id'     => $scheduleFlightRemark->id,
            'field_name'   => 'ALL',
            'old_value'    => json_encode($scheduleFlightRemark->getOriginal()), // Capture full record before deletion
            'new_value'    => null,
            'performed_at' => now(),
        ]);
    }

    /**
     * Handle the ScheduleFlightRemark "restored" event.
     */
    public function restored(ScheduleFlightRemark $scheduleFlightRemark): void
    {
        //
    }

    /**
     * Handle the ScheduleFlightRemark "force deleted" event.
     */
    public function forceDeleted(ScheduleFlightRemark $scheduleFlightRemark): void
    {
        //
    }
}
