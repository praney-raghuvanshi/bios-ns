<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\ScheduleFlightEmail;
use Illuminate\Support\Facades\Auth;

class ScheduleFlightEmailObserver
{
    /**
     * Handle the ScheduleFlightEmail "created" event.
     */
    public function created(ScheduleFlightEmail $scheduleFlightEmail): void
    {
        AuditLog::create([
            'user_id'      => auth()->id(),
            'action'       => 'created',
            'model_type'   => ScheduleFlightEmail::class,
            'model_id'     => $scheduleFlightEmail->id,
            'field_name'   => 'ALL',
            'old_value'    => null,
            'new_value'    => json_encode($scheduleFlightEmail->getAttributes()), // Optional: all fields
            'performed_at' => now(),
        ]);
    }

    /**
     * Handle the ScheduleFlightEmail "updated" event.
     */
    public function updated(ScheduleFlightEmail $scheduleFlightEmail): void
    {
        $userId = Auth::id();

        foreach ($scheduleFlightEmail->getChanges() as $field => $newValue) {
            if ($field === 'updated_at') continue;

            AuditLog::create([
                'user_id'     => $userId,
                'action'      => 'updated',
                'model_type'  => ScheduleFlightEmail::class,
                'model_id'    => $scheduleFlightEmail->id,
                'field_name'  => $field,
                'old_value'   => $scheduleFlightEmail->getOriginal($field),
                'new_value'   => $newValue,
                'performed_at' => now(),
            ]);
        }
    }

    /**
     * Handle the ScheduleFlightEmail "deleted" event.
     */
    public function deleted(ScheduleFlightEmail $scheduleFlightEmail): void
    {
        AuditLog::create([
            'user_id'      => auth()->id(),
            'action'       => 'deleted',
            'model_type'   => ScheduleFlightEmail::class,
            'model_id'     => $scheduleFlightEmail->id,
            'field_name'   => 'ALL',
            'old_value'    => json_encode($scheduleFlightEmail->getOriginal()), // Capture full record before deletion
            'new_value'    => null,
            'performed_at' => now(),
        ]);
    }

    /**
     * Handle the ScheduleFlightEmail "restored" event.
     */
    public function restored(ScheduleFlightEmail $scheduleFlightEmail): void
    {
        //
    }

    /**
     * Handle the ScheduleFlightEmail "force deleted" event.
     */
    public function forceDeleted(ScheduleFlightEmail $scheduleFlightEmail): void
    {
        //
    }
}
