<?php

namespace App\Listeners;

use App\Events\WellbeingCheckCompleted;
use App\Notifications\WellbeingCheckCompletedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class NotifySocialWorkers implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(WellbeingCheckCompleted $event): void
    {
        $caseFile = $event->check->caseFile;

        if (! $caseFile) {
            return;
        }

        $socialWorkers = $caseFile->users()
            ->wherePivot('role', 'social_worker')
            ->get();

        if ($socialWorkers->isEmpty()) {
            return;
        }

        Notification::send(
            $socialWorkers,
            new WellbeingCheckCompletedNotification($event->check, $event->summary)
        );
    }
}
