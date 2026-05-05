<?php

namespace App\Services;

use App\Models\Alert;
use App\Models\WellbeingCheck;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class WellbeingAlertService
{
    private const CRITICAL_QUESTION_THRESHOLD = 25;

    public function evaluate(WellbeingCheck $check, array $scoringSummary): Collection
    {
        $alerts = collect();

        // Only fire tag overrides (safeguarding) and critical responses as alerts.
        // Domain drops and declines are now notifications, not alerts — they were
        // generating too many rows and diluting genuinely urgent safeguarding signals.
        $alerts = $alerts->merge($this->evaluateTagOverrides($check));
        $alerts = $alerts->merge($this->evaluateCriticalResponses($check));

        if ($alerts->isNotEmpty()) {
            Log::info('Wellbeing alerts generated', [
                'check_id'    => $check->id,
                'child_id'    => $check->young_person_id,
                'alert_count' => $alerts->count(),
                'types'       => $alerts->pluck('alert_type')->unique()->values(),
            ]);
        }

        return $alerts;
    }

    /**
     * Fires for any response where a safeguarding tag's alert condition was met.
     * One alert per response/tag pair that fired.
     */
    private function evaluateTagOverrides(WellbeingCheck $check): Collection
    {
        $alerts = collect();

        $responses = $check->responses()->with('question.tags')->get();

        foreach ($responses as $response) {
            foreach ($response->question->tags as $tag) {
                if (
                    ! $tag->alert_override ||
                    $tag->alert_threshold === null ||
                    $response->normalised_score >= $tag->alert_threshold
                ) {
                    continue;
                }

                // Deduplicate: don't create the same tag+check alert twice
                // (guards against re-processing)
                $exists = Alert::where('wellbeing_check_id', $check->id)
                    ->where('tag_id', $tag->id)
                    ->where('alert_type', 'tag_override')
                    ->exists();

                if ($exists) {
                    continue;
                }

                $alerts->push($this->createAlert($check, [
                    'alert_type'  => 'tag_override',
                    'severity'    => 'critical',
                    'response_id' => $response->id,
                    'tag_id'      => $tag->id,
                    'domain_id'   => $response->question->domain_id,
                    'message'     => sprintf(
                        'Safeguarding concern: "%s" tag triggered on "%s" (score: %s/100).',
                        $tag->name,
                        str($response->question->text)->limit(60),
                        round($response->normalised_score)
                    ),
                ]));
            }
        }

        return $alerts;
    }

    /**
     * Fires when a critical-risk-level question scores below 25.
     * Deduplicated per question per check.
     */
    private function evaluateCriticalResponses(WellbeingCheck $check): Collection
    {
        $alerts = collect();

        $criticalResponses = $check->responses()
            ->with('question.domain')
            ->whereHas('question', fn($q) => $q->where('risk_level', 'critical'))
            ->where('normalised_score', '<', self::CRITICAL_QUESTION_THRESHOLD)
            ->get();

        foreach ($criticalResponses as $response) {
            $exists = Alert::where('wellbeing_check_id', $check->id)
                ->where('response_id', $response->id)
                ->where('alert_type', 'critical_response')
                ->exists();

            if ($exists) {
                continue;
            }

            $alerts->push($this->createAlert($check, [
                'alert_type'  => 'critical_response',
                'severity'    => 'high',
                'response_id' => $response->id,
                'tag_id'      => null,
                'domain_id'   => $response->question->domain_id,
                'message'     => sprintf(
                    'Critical response in %s: "%s" scored %s/100.',
                    $response->question->domain->name,
                    str($response->question->text)->limit(60),
                    round($response->normalised_score)
                ),
            ]));
        }

        return $alerts;
    }

    /**
     * Domain drops and declines are surfaced as DATABASE notifications
     * on the social worker's notification bell, not as Alert rows.
     * Call this from WellbeingCheckCompleted listener to notify the SW.
     */
    public function notifyDomainConcerns(WellbeingCheck $check, Collection $domainScores): void
    {
        $previousCheck = WellbeingCheck::where('young_person_id', $check->young_person_id)
            ->where('id', '!=', $check->id)
            ->whereNotNull('completed_at')
            ->orderByDesc('completed_at')
            ->first();

        $previousScores = $previousCheck
            ? $previousCheck->domainScores->keyBy('domain_id')
            : collect();

        $concerns = [];

        foreach ($domainScores as $ds) {
            // Absolute drop below 35
            if ($ds->average_score < WellbeingScoringService::DOMAIN_ALERT_THRESHOLD) {
                $concerns[] = sprintf(
                    '%s domain score is %s/100 — below the alert threshold.',
                    $ds->domain->name,
                    round($ds->average_score)
                );
            }

            // Relative decline of 20+ points since last check
            $prev = $previousScores->get($ds->domain_id);
            if ($prev) {
                $decline = $prev->average_score - $ds->average_score;
                if ($decline >= WellbeingScoringService::DOMAIN_DECLINE_THRESHOLD) {
                    $concerns[] = sprintf(
                        '%s domain declined by %s points (from %s to %s).',
                        $ds->domain->name,
                        round($decline, 1),
                        round($prev->average_score),
                        round($ds->average_score)
                    );
                }
            }
        }

        if (empty($concerns)) {
            return;
        }

        // Notify assigned social worker and carer via Laravel notifications
        $assignedUserIds = \Illuminate\Support\Facades\DB::table('case_user')
            ->join('case_files', 'case_user.case_file_id', '=', 'case_files.id')
            ->where('case_files.young_person_id', $check->young_person_id)
            ->where('case_files.status', 'open')
            ->whereIn('case_user.role', ['social_worker', 'carer'])
            ->pluck('case_user.user_id');

        $users = \App\Models\User::whereIn('id', $assignedUserIds)->get();

        foreach ($users as $user) {
            $user->notify(new \App\Notifications\CareHubNotification(
                type: 'wellbeing_concern',
                summary: count($concerns) . ' domain concern(s) from wellbeing check.',
                data: [
                    'check_id'     => $check->id,
                    'case_file_id' => $check->case_file_id,
                    'concerns'     => $concerns,
                ],
            ));
        }
    }

    private function createAlert(WellbeingCheck $check, array $data): Alert
    {
        return Alert::create([
            'wellbeing_check_id' => $check->id,
            'young_person_id'    => $check->young_person_id,
            'response_id'        => $data['response_id'],
            'tag_id'             => $data['tag_id'],
            'domain_id'          => $data['domain_id'],
            'alert_type'         => $data['alert_type'],
            'severity'           => $data['severity'],
            'message'            => $data['message'],
        ]);
    }
}
