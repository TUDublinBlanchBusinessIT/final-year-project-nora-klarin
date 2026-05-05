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

        // 1. Generate safeguarding alerts (tag_override, critical_response)
        $this->alertService->evaluate($check, $summary);

        // 2. Domain concerns → notification (not alert)
        $this->alertService->notifyDomainConcerns($check, $summary['domain_scores']);

        // 3. Notify social workers the check was completed
        foreach ($this->getAssignedUsers($check->young_person_id, 'social_worker') as $worker) {
            $worker->notify(new CareHubNotification(
                type: 'wellbeing_completed',
                summary: ($child->name ?? 'A young person') . ' completed a wellbeing check.',
                data: [
                    'check_id'     => $check->id,
                    'case_file_id' => $check->case_file_id,
                    'risk_level'   => $check->risk_level,
                ],
            ));
        }
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
