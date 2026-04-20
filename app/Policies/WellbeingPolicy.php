<?php

namespace App\Policies;

use App\Models\Alert;
use App\Models\User;
use App\Models\WellbeingCheck;

/**
 * WellbeingPolicy
 *
 * Defines authorization rules for wellbeing check and alert actions.
 *
 * Register in AuthServiceProvider:
 *
 *   protected $policies = [
 *       WellbeingCheck::class => WellbeingPolicy::class,
 *       Alert::class          => WellbeingPolicy::class,
 *   ];
 *
 * Role assumptions (matching your users.role column):
 *   'young_person' — the child/young person in care
 *   'social_worker'— assigned social worker
 *   'carer'        — foster carer
 */
class WellbeingPolicy
{
    /**
     * Only the authenticated young person can start their own check.
     */
    public function startWellbeingCheck(User $user, User $youngPerson): bool
    {
        return $user->id === $youngPerson->id
            && $user->role === 'young_person';
    }

    /**
     * Only the young person who owns the check can submit it.
     */
    public function submitWellbeingCheck(User $user, WellbeingCheck $check): bool
    {
        return $user->id === $check->young_person_id
            && $user->role === 'young_person';
    }

    /**
     * Social workers and carers assigned to a young person's case can view
     * their wellbeing history.
     */
    public function viewWellbeingHistory(User $user, User $youngPerson): bool
    {
        if (!in_array($user->role, ['social_worker', 'carer'])) {
            return false;
        }

        return $this->isAssignedToCase($user, $youngPerson);
    }

    /**
     * Social workers and carers can view alerts for their assigned cases.
     * (Used as a gate check — no model needed.)
     */
    public function viewAlerts(User $user): bool
    {
        return in_array($user->role, ['social_worker', 'carer']);
    }

    /**
     * Social workers and carers can acknowledge alerts for their cases.
     */
    public function acknowledgeAlert(User $user, Alert $alert): bool
    {
        if (!in_array($user->role, ['social_worker', 'carer'])) {
            return false;
        }

        // Can only acknowledge alerts for young people on their cases
        $youngPerson = User::find($alert->young_person_id);

        return $youngPerson && $this->isAssignedToCase($user, $youngPerson);
    }

    /**
     * Checks whether the given worker/carer is assigned to the young
     * person's active case via the case_user pivot table.
     */
    private function isAssignedToCase(User $user, User $youngPerson): bool
    {
        return \DB::table('case_user')
            ->join('case_files', 'case_user.case_file_id', '=', 'case_files.id')
            ->where('case_user.user_id', $user->id)
            ->where('case_files.young_person_id', $youngPerson->id)
            ->where('case_files.status', 'open')
            ->exists();
    }
}
