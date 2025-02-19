<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Spatie\Activitylog\Facades\Activity;

class LogSuccessfulLogout
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Logout $event): void
    {
        Activity::event('Logout')->causedBy($event->user)->useLog('Authentication')->log('Logout successful');
    }
}
