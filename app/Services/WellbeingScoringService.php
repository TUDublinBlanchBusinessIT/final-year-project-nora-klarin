<?php

namespace App\Services;

use App\Models\WellbeingCheck;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WellbeingScoringService
{

    private const RISK_THRESHOLD_CRITICAL = 250;
    private const RISK_THRESHOLD_HIGH     = 150;
    private const RISK_THRESHOLD_MODERATE = 80;

    public const DOMAIN_ALERT_THRESHOLD = 35;

    public const DOMAIN_DECLINE_THRESHOLD = 20;

    private const RISK_WEIGHTS = [
        'low'      => 0.5,
        'medium'   => 1.0,
        'high'     => 2.0,
        'critical' => 4.0,
    ];

    /**
     * Main entry point. Processes a completed check through the full pipeline.
     * Returns a summary array consumed by WellbeingAlertService.
     *
     * @param  WellbeingCheck $check  A check with responses already stored
     * @return array{
     *   overall_wb_score: float,
     *   overall_risk_score: float,
     *   risk_classification: string,
     *   domain_scores: Collection,
     *   tag_patterns: array,
     *   safeguarding_triggered: bool
     * }
     */
    public function process(WellbeingCheck $check): array
    {
        $this->scoreResponses($check);

        $domainScores = $this->calculateDomainScores($check);

        [$overallWb, $overallRisk] = $this->computeOverallScores($check, $domainScores);

        [$tagPatterns, $safeguardingTriggered] = $this->analyseTagPatterns($check);

        return [
            'overall_wb_score'       => $overallWb,
            'overall_risk_score'     => $overallRisk,
            'risk_classification'    => $this->classifyRisk($overallRisk),
            'domain_scores'          => $domainScores,
            'tag_patterns'           => $tagPatterns,
            'safeguarding_triggered' => $safeguardingTriggered,
        ];
    }

    public function scoreResponses(WellbeingCheck $check): void
    {
        $responses = $check->responses()->with('question')->get();

        foreach ($responses as $response) {
            $question = $response->question;

            $wbScore         = $this->normalise(
                $response->raw_value,
                $question->min_value,
                $question->max_value,
                (bool) $question->is_positive
            );

            $riskContribution = $this->riskContribution(
                $wbScore,
                self::RISK_WEIGHTS[$question->risk_level] ?? 1.0
            );

            $response->update([
                'normalised_score'  => $wbScore,
                'risk_contribution' => $riskContribution,
            ]);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Step 2 — Domain score aggregation
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Groups scored responses by domain and writes/updates domain score rows.
     * Uses updateOrCreate to be safe against re-processing the same check.
     *
     * Domain wb score  = average of question wb scores within the domain
     * Domain risk score = sum of risk contributions within the domain
     *
     * @return Collection<int, \App\Models\WellbeingDomainScore>
     */
    public function calculateDomainScores(WellbeingCheck $check): Collection
    {
        $responses = $check->responses()
            ->with('question.domain')
            ->get();

        $grouped = $responses->groupBy(fn($r) => $r->question->domain_id);

        foreach ($grouped as $domainId => $items) {
            $avgWbScore  = round($items->avg('normalised_score'), 2);
            $riskScore   = round($items->sum('risk_contribution'), 2);

            $check->domainScores()->updateOrCreate(
                ['domain_id' => $domainId],
                [
                    'average_score' => $avgWbScore,
                    'risk_score'    => $riskScore,
                ]
            );
        }

        return $check->domainScores()->with('domain')->get();
    }


    /**
     * Computes and persists overall wellbeing and risk scores for the check.
     *
     * Overall wb score   = average of domain wb scores (equal domain weighting,
     *                      consistent with OECD framework — each domain is an
     *                      independent wellbeing dimension, not a subscale)
     * Overall risk score = sum of all domain risk scores (cumulative — multiple
     *                      concerns across domains should compound, not cancel)
     *
     * @return array{float, float} [overall_wb_score, overall_risk_score]
     */
    public function computeOverallScores(WellbeingCheck $check, Collection $domainScores): array
    {
        $overallWb   = round($domainScores->avg('average_score'), 2);
        $overallRisk = round($domainScores->sum('risk_score'), 2);

        $check->update([
            'overall_wb_score'   => $overallWb,
            'overall_risk_score' => $overallRisk,
        ]);

        return [$overallWb, $overallRisk];
    }


    /**
     * Analyses tag patterns across all responses in a check.
     *
     * For each tag associated with a question, records:
     *   - how many responses in this check touched the tag
     *   - the average wb_score across those responses
     *   - whether the score was below the tag's alert_threshold
     *
     * Safeguarding detection:
     *   A tag fires a safeguarding concern when ALL of these are true:
     *     1. tag.alert_override = true
     *     2. tag.alert_threshold is set
     *     3. the response wb_score < tag.alert_threshold
     *
     *   This replaces the old is_safeguarding boolean + hardcoded < 40 check,
     *   using the per-tag alert_threshold from the updated schema.
     *
     * Returns the tag pattern array and a boolean indicating whether any
     * safeguarding tag was triggered. The alert service consumes both.
     *
     * @return array{array, bool}
     */
    public function analyseTagPatterns(WellbeingCheck $check): array
    {
        $responses = $check->responses()
            ->with('question.tags')
            ->get();

        $tagAccumulator      = [];
        $safeguardingTriggered = false;
        $firedTagIds         = [];

        foreach ($responses as $response) {
            foreach ($response->question->tags as $tag) {

                if (!isset($tagAccumulator[$tag->name])) {
                    $tagAccumulator[$tag->name] = [
                        'tag_id'       => $tag->id,
                        'category'     => $tag->category,
                        'count'        => 0,
                        'score_sum'    => 0,
                        'average_score'=> 0,
                        'alert_fired'  => false,
                    ];
                }

                $tagAccumulator[$tag->name]['count']++;
                $tagAccumulator[$tag->name]['score_sum'] += $response->normalised_score;

                $threshold = $tag->alert_threshold;

                if (
                    $tag->alert_override &&
                    $threshold !== null &&
                    $response->normalised_score < $threshold
                ) {
                    $safeguardingTriggered = true;
                    $tagAccumulator[$tag->name]['alert_fired'] = true;
                    $firedTagIds[] = $tag->id;

                    Log::warning('Safeguarding tag fired', [
                        'check_id'    => $check->id,
                        'tag'         => $tag->name,
                        'wb_score'    => $response->normalised_score,
                        'threshold'   => $threshold,
                        'response_id' => $response->id,
                    ]);
                }

                if ($tag->alert_override && $tagAccumulator[$tag->name]['alert_fired']) {
                    $response->update([
                        'tags_fired' => array_unique(
                            array_merge(
                                (array) json_decode($response->tags_fired ?? '[]'),
                                [$tag->id]
                            )
                        ),
                    ]);
                }
            }
        }

        $tagPatterns = [];
        foreach ($tagAccumulator as $tagName => $data) {
            $tagPatterns[$tagName] = array_merge($data, [
                'average_score' => round($data['score_sum'] / $data['count'], 2),
            ]);
            unset($tagPatterns[$tagName]['score_sum']);
        }

        return [$tagPatterns, $safeguardingTriggered];
    }


    /**
     * Normalises a raw response value to a 0–100 wellbeing score.
     *
     * Formula:
     *   normalised = (raw − min) / (max − min)
     *   wb_score   = normalised × 100              (positive framing)
     *   wb_score   = (1 − normalised) × 100        (negative framing)
     *
     * Negative framing (reverse coding) ensures that high scores always
     * represent stronger wellbeing regardless of how the question was worded,
     * consistent with established psychometric practice (Tennant et al., 2007).
     */
    public function normalise(int $raw, int $min, int $max, bool $isPositive): float
    {
        if ($max === $min) {
            return 50.0; // Degenerate case — return neutral score
        }

        $normalised = ($raw - $min) / ($max - $min);

        if (!$isPositive) {
            $normalised = 1 - $normalised;
        }

        return round($normalised * 100, 2);
    }


    public function riskContribution(float $wbScore, float $weight): float
    {
        return round((100 - $wbScore) * $weight, 2);
    }

    public function classifyRisk(float $riskScore): string
    {
        return match(true) {
            $riskScore >= self::RISK_THRESHOLD_CRITICAL => 'critical',
            $riskScore >= self::RISK_THRESHOLD_HIGH     => 'high',
            $riskScore >= self::RISK_THRESHOLD_MODERATE => 'moderate',
            default                                     => 'low',
        };
    }

    public function getRiskWeight(string $riskLevel): float
    {
        return self::RISK_WEIGHTS[$riskLevel] ?? 1.0;
    }
}
