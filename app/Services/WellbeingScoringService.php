<?php

namespace App\Services;

use App\Models\Question;

class WellbeingScoringService
{
    public function normalise($raw, $min, $max, $isPositive)
    {
        $normalised = ($raw - $min) / ($max - $min);

        if (!$isPositive) {
            $normalised = 1 - $normalised;
        }

        return round($normalised * 100, 2);
    }

    public function riskContribution($wbScore, $weight)
    {
        return round((100 - $wbScore) * $weight, 2);
    }

    public function calculateDomainScores($check)
    {
        $responses = $check->responses()->with('question.domain')->get();

        $grouped = $responses->groupBy(fn($r) => $r->question->domain_id);

        foreach ($grouped as $domainId => $items) {

            $avgScore = round($items->avg('normalised_score'), 2);
            $riskScore = round($items->sum('risk_contribution'), 2);

            $check->domainScores()->create([
                'domain_id' => $domainId,
                'average_score' => $avgScore,
                'risk_score' => $riskScore,
            ]);
        }
    }

    public function overallFromDomains($check)
    {
        $domainScores = $check->domainScores;

        return round($domainScores->avg('average_score'), 2);
    }

    public function overallRiskFromDomains($check)
    {
        return round($check->domainScores->sum('risk_score'), 2);
    }

    public function classifyRisk($riskScore)
    {
        if ($riskScore >= 250) return 'critical';
        if ($riskScore >= 150) return 'high';
        if ($riskScore >= 80) return 'moderate';
        return 'low';
    }

    public function analyseTagPatterns($check)
{
    $responses = $check->responses()->with('question.tags')->get();

    $tagScores = [];
    $safeguardingTriggered = false;

    foreach ($responses as $response) {

        foreach ($response->question->tags as $tag) {

            if (!isset($tagScores[$tag->name])) {
                $tagScores[$tag->name] = [
                    'count' => 0,
                    'average_score' => 0,
                ];
            }

            $tagScores[$tag->name]['count']++;
            $tagScores[$tag->name]['average_score'] += $response->normalised_score;

            if ($tag->is_safeguarding && $response->normalised_score < 40) {
                $safeguardingTriggered = true;
            }
        }
    }

    foreach ($tagScores as $tag => $data) {
        $tagScores[$tag]['average_score'] =
            round($data['average_score'] / $data['count'], 2);
    }

    $check->update([
        'tag_summary' => $tagScores,
        'safeguarding_flag' => $safeguardingTriggered
    ]);

    return $safeguardingTriggered;
}
}

