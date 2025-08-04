<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\ScheduleFlight;
use Illuminate\Support\Facades\Auth;

class ScheduleFlightObserver
{
    /**
     * Handle the ScheduleFlight "created" event.
     */
    public function created(ScheduleFlight $scheduleFlight): void
    {
        AuditLog::create([
            'user_id'      => auth()->id(),
            'action'       => 'created',
            'model_type'   => ScheduleFlight::class,
            'model_id'     => $scheduleFlight->id,
            'field_name'   => 'ALL',
            'old_value'    => null,
            'new_value'    => json_encode($scheduleFlight->getAttributes()), // Optional: all fields
            'performed_at' => now(),
        ]);
    }

    /**
     * Handle the ScheduleFlight "updated" event.
     */
    public function updated(ScheduleFlight $scheduleFlight): void
    {
        $userId = Auth::id();

        foreach ($scheduleFlight->getChanges() as $field => $newValue) {
            $oldValue = $scheduleFlight->getOriginal($field);

            if ($field === 'updated_at' || $field === 'departure_time_diff' || $field === 'arrival_time_diff' || $oldValue == $newValue) continue;

            if ($field === 'estimated_departure_time') {
                $fieldName = 'EDT';
            } else if ($field === 'actual_departure_time') {
                $fieldName = 'ADT';
            } else if ($field === 'estimated_arrival_time') {
                $fieldName = 'ETA';
            } else if ($field === 'actual_arrival_time') {
                $fieldName = 'ATA';
            } else if ($field === 'uplifted') {
                $fieldName = 'Uplifted Weight';
            } else if ($field === 'utilisation') {
                $fieldName = 'Utilisation';
            } else if ($field === 'offloaded') {
                $fieldName = 'Offloaded Weight';
            } else if ($field === 'latest_remark') {
                $fieldName = 'Latest Remark';
            } else if ($field === 'aircraft_id') {
                $fieldName = 'Aircraft ID';
            }

            AuditLog::create([
                'user_id'     => $userId,
                'action'      => 'updated',
                'model_type'  => ScheduleFlight::class,
                'model_id'    => $scheduleFlight->id,
                'field_name'  => $field,
                'old_value'   => $oldValue,
                'new_value'   => $newValue,
                'description' => "For Schedule Flight : $fieldName changed from $oldValue to $newValue.",
                'performed_at' => now(),
            ]);
        }
    }

    /**
     * Handle the ScheduleFlight "deleted" event.
     */
    public function deleted(ScheduleFlight $scheduleFlight): void
    {
        AuditLog::create([
            'user_id'      => auth()->id(),
            'action'       => 'deleted',
            'model_type'   => ScheduleFlight::class,
            'model_id'     => $scheduleFlight->id,
            'field_name'   => 'ALL',
            'old_value'    => json_encode($scheduleFlight->getOriginal()), // Capture full record before deletion
            'new_value'    => null,
            'performed_at' => now(),
        ]);
    }

    /**
     * Handle the ScheduleFlight "restored" event.
     */
    public function restored(ScheduleFlight $scheduleFlight): void
    {
        //
    }

    /**
     * Handle the ScheduleFlight "force deleted" event.
     */
    public function forceDeleted(ScheduleFlight $scheduleFlight): void
    {
        //
    }
}
