<?php

namespace App\Listeners;

use App\Events\WellbeingCheckCompleted;
use App\Models\User;
use App\Notifications\CareHubNotification;
use App\Services\WellbeingAlertService;
use Illuminate\Support\Facades\DB;

class WellbeingCheckCompletedListener
{
    public function __construct(private readonly WellbeingAlertService $alertService) {}

    public function handle(WellbeingCheckCompleted $event): void
    {
        $check   = $event->check;
        $summary = $event->summary;
        $child   = $check->youngPerson;

        $this->alertService->evaluate($check, $summary);


    }

    private function getAssignedUsers(int $youngPersonId, string $role)
    {
        $ids = DB::table('case_user')
            ->join('case_files', 'case_user.case_file_id', '=', 'case_files.id')
            ->where('case_files.young_person_id', $youngPersonId)
            ->where('case_files.status', 'open')
            ->where('case_user.role', $role)
            ->pluck('case_user.user_id');

        return User::whereIn('id', $ids)->get();
    }
}
