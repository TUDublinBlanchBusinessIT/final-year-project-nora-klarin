<?php

namespace App\Policies;

use App\Models\Alert;
use App\Models\User;
use App\Models\WellbeingCheck;

class WellbeingPolicy
{
    private const MIN_SELF_REPORTING_AGE = 12;

    
    public function startWellbeingCheck(User $user, User $youngPerson): bool
    {
        \Log::info('WellbeingPolicy::startWellbeingCheck', [
            'user_id' => $user->id,
            'user_role' => $user->role,
            'youngPerson_id' => $youngPerson->id,
            'youngPerson_role' => $youngPerson->role,
            'check1_same_id' => $user->id === $youngPerson->id,
            'check2_role_match' => $user->role === 'young_person',
            'combined' => $user->id === $youngPerson->id && $user->role === 'young_person',
        ]);
        
        if ($user->id === $youngPerson->id && $user->role === 'young_person') {
            $selfReportAllowed = $this->canSelfReport($youngPerson);
            \Log::info('Self-report check: ' . ($selfReportAllowed ? 'ALLOWED' : 'DENIED'), [
                'age' => $youngPerson->age(),
                'min_age' => self::MIN_SELF_REPORTING_AGE,
            ]);
            return $selfReportAllowed;
        }

        if (! in_array($user->role, ['social_worker', 'carer'])) {
            \Log::info('Role not in allowed list');
            return false;
        }

        $assigned = $this->isAssignedToCase($user, $youngPerson);
        \Log::info('isAssignedToCase: ' . ($assigned ? 'YES' : 'NO'));
        return $assigned;
    }

    /**
     * The young person can submit their own check, and assigned staff
     * or carers can submit on behalf of their assigned child.
     */
    public function submitWellbeingCheck(User $user, WellbeingCheck $check): bool
    {
        if ($user->id === $check->young_person_id && $user->role === 'young_person') {
            return $this->canSelfReport($user);
        }

        if (! in_array($user->role, ['social_worker', 'carer'])) {
            return false;
        }

        $youngPerson = User::find($check->young_person_id);

        return $youngPerson && $this->isAssignedToCase($user, $youngPerson);
    }

    private function canSelfReport(User $youngPerson): bool
    {
        return $youngPerson->age() !== null && $youngPerson->age() >= self::MIN_SELF_REPORTING_AGE;
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
