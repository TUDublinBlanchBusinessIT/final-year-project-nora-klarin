<?php

namespace App\Services;

use App\Models\Alert;
use App\Models\WellbeingCheck;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class WellbeingAlertService
{
    // wb_score below this on a critical-risk-level question fires an alert
    private const CRITICAL_QUESTION_THRESHOLD = 25;

    public function __construct(
        private readonly WellbeingScoringService $scoringService
    ) {}

    /**
     * Main entry point. Evaluates all alert conditions for a processed check.
     * Returns a collection of Alert models that were created.
     *
     * @param  WellbeingCheck $check
     * @param  array          $scoringSummary  Return value of WellbeingScoringService::process()
     * @return Collection<Alert>
     */
    public function evaluate(WellbeingCheck $check, array $scoringSummary): Collection
    {
        $alerts = collect();

        // 1. Tag override alerts (highest priority — safeguarding)
        $alerts = $alerts->merge(
            $this->evaluateTagOverrides($check)
        );

        // 2. Critical question alerts
        $alerts = $alerts->merge(
            $this->evaluateCriticalResponses($check)
        );

        // 3. Domain drop alerts (absolute threshold)
        $alerts = $alerts->merge(
            $this->evaluateDomainDrops($check, $scoringSummary['domain_scores'])
        );

        // 4. Domain decline alerts (relative to previous check)
        $alerts = $alerts->merge(
            $this->evaluateDomainDeclines($check, $scoringSummary['domain_scores'])
        );

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

    // ─────────────────────────────────────────────────────────────────────────
    // Alert condition 1 — Tag overrides
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Fires for any response where a safeguarding tag's alert condition was met.
     *
     * Condition: tag.alert_override = true AND response.wb_score < tag.alert_threshold
     *
     * One alert is created per response/tag pair that fired, so that each
     * concern is independently visible and acknowledgeable by the social worker.
     */
    private function evaluateTagOverrides(WellbeingCheck $check): Collection
    {
        $alerts = collect();

        $responses = $check->responses()
            ->with('question.tags')
            ->get();

        foreach ($responses as $response) {
            foreach ($response->question->tags as $tag) {

                if (
                    !$tag->alert_override ||
                    $tag->alert_threshold === null ||
                    $response->normalised_score >= $tag->alert_threshold
                ) {
                    continue;
                }

                $alert = $this->createAlert($check, [
                    'alert_type'  => 'tag_override',
                    'severity'    => 'critical',
                    'response_id' => $response->id,
                    'tag_id'      => $tag->id,
                    'domain_id'   => $response->question->domain_id,
                    'message'     => sprintf(
                        'Safeguarding concern: "%s" tag triggered on question "%s" (score: %s/100).',
                        $tag->name,
                        str($response->question->text)->limit(60),
                        $response->normalised_score
                    ),
                ]);

                $alerts->push($alert);
            }
        }

        return $alerts;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Alert condition 2 — Critical-risk questions with very low scores
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Fires when a question with risk_level = 'critical' scores below
     * CRITICAL_QUESTION_THRESHOLD (25), even if no safeguarding tag fired.
     *
     * This catches high-risk questions that don't carry a safeguarding tag
     * but still warrant urgent attention — e.g. feeling completely unsafe
     * at home, zero social support, severe loneliness.
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
            $alert = $this->createAlert($check, [
                'alert_type'  => 'critical_response',
                'severity'    => 'high',
                'response_id' => $response->id,
                'tag_id'      => null,
                'domain_id'   => $response->question->domain_id,
                'message'     => sprintf(
                    'Critical response in %s domain: "%s" scored %s/100.',
                    $response->question->domain->name,
                    str($response->question->text)->limit(60),
                    $response->normalised_score
                ),
            ]);

            $alerts->push($alert);
        }

        return $alerts;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Alert condition 3 — Domain drop (absolute threshold)
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Fires when any domain's average wellbeing score falls below
     * WellbeingScoringService::DOMAIN_ALERT_THRESHOLD (35).
     *
     * Severity is graduated by how far below the threshold the score is:
     *   < 20  → critical
     *   < 28  → high
     *   < 35  → medium
     */
    private function evaluateDomainDrops(WellbeingCheck $check, Collection $domainScores): Collection
    {
        $alerts = collect();
        $threshold = WellbeingScoringService::DOMAIN_ALERT_THRESHOLD;

        foreach ($domainScores as $domainScore) {
            if ($domainScore->average_score >= $threshold) {
                continue;
            }

            $severity = match(true) {
                $domainScore->average_score < 20 => 'critical',
                $domainScore->average_score < 28 => 'high',
                default                          => 'medium',
            };

            $alert = $this->createAlert($check, [
                'alert_type'  => 'domain_drop',
                'severity'    => $severity,
                'response_id' => null,
                'tag_id'      => null,
                'domain_id'   => $domainScore->domain_id,
                'message'     => sprintf(
                    '%s domain wellbeing score is %s/100 — below the alert threshold of %s.',
                    $domainScore->domain->name,
                    $domainScore->average_score,
                    $threshold
                ),
            ]);

            $alerts->push($alert);
        }

        return $alerts;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Alert condition 4 — Domain decline (relative to previous check)
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Fires when a domain has declined by DOMAIN_DECLINE_THRESHOLD (20+) points
     * compared to the same domain in the child's previous check.
     *
     * This catches acute deterioration even when the absolute score is still
     * above the drop threshold — e.g. a child going from 70 to 45 in the
     * emotional domain is flagged even though 45 is above 35.
     *
     * Compares against the most recent prior check, skipping any domain
     * not present in the previous check (first occurrence of that domain).
     */
    private function evaluateDomainDeclines(WellbeingCheck $check, Collection $currentDomainScores): Collection
    {
        $alerts = collect();
        $declineThreshold = WellbeingScoringService::DOMAIN_DECLINE_THRESHOLD;

        // Find the previous completed check for this young person
        $previousCheck = WellbeingCheck::where('young_person_id', $check->young_person_id)
            ->where('id', '!=', $check->id)
            ->whereNotNull('completed_at')
            ->orderByDesc('completed_at')
            ->first();

        if (!$previousCheck) {
            return $alerts; // No previous check to compare against
        }

        $previousDomainScores = $previousCheck->domainScores
            ->keyBy('domain_id');

        foreach ($currentDomainScores as $current) {
            $previous = $previousDomainScores->get($current->domain_id);

            if (!$previous) {
                continue; // Domain not present in previous check
            }

            $decline = $previous->average_score - $current->average_score;

            if ($decline < $declineThreshold) {
                continue;
            }

            $alert = $this->createAlert($check, [
                'alert_type'  => 'domain_decline',
                'severity'    => $decline >= 35 ? 'high' : 'medium',
                'response_id' => null,
                'tag_id'      => null,
                'domain_id'   => $current->domain_id,
                'message'     => sprintf(
                    '%s domain declined by %s points (from %s to %s) since last check.',
                    $current->domain->name,
                    round($decline, 1),
                    $previous->average_score,
                    $current->average_score
                ),
            ]);

            $alerts->push($alert);
        }

        return $alerts;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Helper
    // ─────────────────────────────────────────────────────────────────────────

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
