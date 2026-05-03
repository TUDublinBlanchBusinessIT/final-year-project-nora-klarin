<?php

namespace App\Listeners;

use App\Events\WellbeingCheckCompleted;
use App\Services\WellbeingAlertService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateWellbeingAlerts implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct(private readonly WellbeingAlertService $alertService)
    {
    }

    public function handle(WellbeingCheckCompleted $event): void
    {
        $this->alertService->evaluate($event->check, $event->summary);
    }
}
