<?php

namespace App\Services;

use App\Events\WellbeingCheckCompleted;
use App\Models\User;
use App\Models\WellbeingCheck;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WellbeingCheckProcessor
{
    public function __construct(private readonly WellbeingScoringService $scoringService)
    {
    }

    public function submitResponses(WellbeingCheck $check, array $responses, User $respondent): array
    {
        $this->validateQuestionMembership($check, $responses);

        $respondentType = $respondent->role === 'young_person' ? 'young_person' : 'carer';

        $summary = DB::transaction(function () use ($check, $responses, $respondentType) {
            foreach ($responses as $response) {
                $check->responses()->create([
                    'question_id'      => $response['question_id'],
                    'raw_value'        => $response['raw_value'],
                    'normalised_score' => 0,
                    'risk_contribution'=> 0,
                    'respondent_type'  => $respondentType,
                ]);
            }

            $summary = $this->scoringService->process($check);

            $check->update([
                'completed_at' => now(),
                'submitted_by' => auth()->id(),
                'risk_level'   => $summary['risk_classification'],
            ]);

            $this->syncCaseRisk($check, $summary['risk_classification']);

            return $summary;
        });

        event(new WellbeingCheckCompleted($check->refresh(), $summary));

        return $summary;
    }

    /**
     * Verifies every submitted question_id was actually selected for this check.
     * Throws a ValidationException so the controller returns a clean 422
     * rather than silently storing responses for unrelated questions.
     *
     * This is a single bulk query regardless of how many responses are submitted.
     */
    private function validateQuestionMembership(WellbeingCheck $check, array $responses): void
    {
        $submittedIds = collect($responses)->pluck('question_id')->map(fn($id) => (int) $id);

        $validIds = DB::table('check_question_log')
            ->where('wellbeing_check_id', $check->id)
            ->pluck('question_id')
            ->map(fn($id) => (int) $id);

        $invalid = $submittedIds->diff($validIds);

        if ($invalid->isNotEmpty()) {
            throw ValidationException::withMessages([
                'responses' => 'One or more questions were not part of this check: '
                    . $invalid->join(', ') . '.',
            ]);
        }
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
            'critical', 'high' => 'high',
            'moderate'         => 'medium',
            default            => 'low',
        };
    }
}
