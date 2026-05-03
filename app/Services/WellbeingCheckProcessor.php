<?php

namespace App\Services;

use App\Events\WellbeingCheckCompleted;
use App\Models\User;
use App\Models\WellbeingCheck;
use Illuminate\Support\Facades\DB;

class WellbeingCheckProcessor
{
    public function __construct(private readonly WellbeingScoringService $scoringService)
    {
    }

    public function submitResponses(WellbeingCheck $check, array $responses, User $respondent): array
    {
        $summary = DB::transaction(function () use ($check, $responses, $respondent) {
            foreach ($responses as $response) {
                $check->responses()->create([
                    'question_id'     => $response['question_id'],
                    'raw_value'       => $response['raw_value'],
                    'normalised_score'=> 0,
                    'risk_contribution'=> 0,
                    'respondent_type' => $respondent->role,
                ]);
            }

            $summary = $this->scoringService->process($check);

            $check->update([
                'completed_at'      => now(),
                'overall_score'     => $summary['overall_wb_score'],
                'overall_risk_score'=> $summary['overall_risk_score'],
                'risk_level'        => $summary['risk_classification'],
            ]);

            $this->syncCaseRisk($check, $summary['risk_classification']);

            return $summary;
        });

        event(new WellbeingCheckCompleted($check->refresh(), $summary));

        return $summary;
    }

    private function syncCaseRisk(WellbeingCheck $check, string $riskLevel): void
    {
        if (! $check->caseFile) {
            return;
        }

        $check->caseFile->update([
            'risk_level' => $this->mapCaseRiskLevel($riskLevel),
        ]);
    }

    private function mapCaseRiskLevel(string $riskLevel): string
    {
        return match ($riskLevel) {
            'critical' => 'high',
            'high'     => 'high',
            'medium'   => 'medium',
            default    => 'low',
        };
    }
}
