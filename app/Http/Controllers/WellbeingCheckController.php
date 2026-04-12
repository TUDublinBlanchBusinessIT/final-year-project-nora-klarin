<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubmitWellbeingCheckRequest;
use App\Models\User;
use App\Models\WellbeingCheck;
use App\Services\CheckQuestionSelector;
use App\Services\WellbeingAlertService;
use App\Services\WellbeingScoringService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * WellbeingCheckController
 *
 * Handles the four core wellbeing check API endpoints:
 *
 *   POST   /api/wellbeing/start
 *   POST   /api/wellbeing/{check}/submit
 *   GET    /api/wellbeing/{youngPerson}/history
 *   GET    /api/alerts/unacknowledged
 *
 * All endpoints require authentication via Sanctum.
 * start() and submit() are restricted to the young person themselves.
 * history() and unacknowledged() are restricted to social workers and carers.
 */
class WellbeingCheckController extends Controller
{
    public function __construct(
        private readonly CheckQuestionSelector  $selector,
        private readonly WellbeingScoringService $scoringService,
        private readonly WellbeingAlertService   $alertService,
    ) {}

    // ─────────────────────────────────────────────────────────────────────────
    // POST /api/wellbeing/start
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Starts a new wellbeing check for the authenticated young person.
     *
     * 1. Creates a pending WellbeingCheck record
     * 2. Selects and resolves questions via CheckQuestionSelector
     * 3. Logs selected question IDs to check_question_log for rotation tracking
     * 4. Returns the check ID and question list to the frontend
     *
     * The frontend uses the returned questions to render the gamified check UI.
     * The check_id must be stored client-side and sent with the submit request.
     *
     * @return JsonResponse
     */
    public function start(Request $request): JsonResponse
    {
        /** @var User $youngPerson */
        $youngPerson = Auth::user();

        $this->authorize('startWellbeingCheck', $youngPerson);

        // Determine check type — intake if this is their first check
        $isIntake = !WellbeingCheck::where('young_person_id', $youngPerson->id)
            ->whereNotNull('completed_at')
            ->exists();

        $check = WellbeingCheck::create([
            'young_person_id' => $youngPerson->id,
            'case_file_id'    => $this->resolveCaseFileId($youngPerson),
            'check_type'      => $isIntake ? 'intake' : 'scheduled',
            'game_mode'       => $request->input('game_mode', 'slider'),
        ]);

        // Select age-appropriate questions for this check
        $questions = $this->selector->selectFor($youngPerson);

        // Log selected questions for rotation (prevents repeat in next N checks)
        foreach ($questions as $question) {
            DB::table('check_question_log')->insert([
                'wellbeing_check_id' => $check->id,
                'question_id'        => $question->id,
                'created_at'         => now(),
            ]);
        }

        return response()->json([
            'check_id'  => $check->id,
            'check_type'=> $check->check_type,
            'questions' => $questions->map(fn($q) => [
                'id'            => $q->id,
                'domain'        => $q->domain_name,
                'text'          => $q->text,
                'response_type' => $q->response_type,
                'min_value'     => $q->min_value,
                'max_value'     => $q->max_value,
            ]),
        ], 201);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // POST /api/wellbeing/{check}/submit
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Submits completed responses for a wellbeing check and runs the full
     * scoring and alert pipeline.
     *
     * Expects a JSON body:
     * {
     *   "responses": [
     *     { "question_id": 1, "raw_value": 3 },
     *     { "question_id": 4, "raw_value": 0 },
     *     ...
     *   ]
     * }
     *
     * Pipeline:
     *   1. Validate responses (all questions belong to this check, values in range)
     *   2. Persist raw responses
     *   3. Run WellbeingScoringService::process() — scores + domain aggregation
     *   4. Run WellbeingAlertService::evaluate() — generate alerts
     *   5. Mark check as completed
     *   6. Return summary (scores, risk classification, any alerts generated)
     *
     * The entire pipeline runs in a transaction — if any step fails, no
     * partial data is written.
     *
     * @param  SubmitWellbeingCheckRequest $request
     * @param  WellbeingCheck              $check
     * @return JsonResponse
     */
    public function submit(SubmitWellbeingCheckRequest $request, WellbeingCheck $check): JsonResponse
    {
        $this->authorize('submitWellbeingCheck', $check);

        // Prevent re-submission of a completed check
        if ($check->completed_at !== null) {
            return response()->json([
                'message' => 'This check has already been submitted.',
            ], 409);
        }

        $result = DB::transaction(function () use ($request, $check) {

            // Persist raw responses
            foreach ($request->validated('responses') as $response) {
                $check->responses()->create([
                    'question_id' => $response['question_id'],
                    'raw_value'   => $response['raw_value'],
                    // normalised_score and risk_contribution populated by scoring service
                    'normalised_score'  => 0,
                    'risk_contribution' => 0,
                ]);
            }

            $summary = $this->scoringService->process($check);

            foreach ($summary['domain_scores'] as $domainScore) {
                DB::table('wellbeing_domain_scores')->insert([
                    'wellbeing_check_id' => $check->id,
                    'domain_id'          => $domainScore->domain->id,
                    'average_score'      => $domainScore->average_score,
                    'risk_score'         => $domainScore->risk_score,
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ]);
            }
            $check->update([
            'overall_wb_score'   => $summary['overall_wb_score'],
            'overall_risk_score' => $summary['overall_risk_score'],
            'risk_level'         => $summary['risk_classification'],
        ]);

            // Run alert evaluation
            $alerts = $this->alertService->evaluate($check, $summary);

            // Mark check complete
            $check->update(['completed_at' => now()]);

            return [$summary, $alerts];
        });

        [$summary, $alerts] = $result;

        return response()->json([
            'message'             => 'Check submitted successfully.',
            'overall_wb_score'    => $summary['overall_wb_score'],
            'overall_risk_score'  => $summary['overall_risk_score'],
            'risk_classification' => $summary['risk_classification'],
            'domain_scores'       => $summary['domain_scores']->map(fn($ds) => [
                'domain'        => $ds->domain->name,
                'wb_score'      => $ds->average_score,
                'risk_score'    => $ds->risk_score,
            ]),
            'alerts_generated'    => $alerts->count(),
            'safeguarding_flag'   => $summary['safeguarding_triggered'],
        ], 200);
    }


    /**
     * Returns the wellbeing check history for a young person.
     *
     * Used by the social worker dashboard to render longitudinal trend charts.
     *
     * Returns all completed checks with their domain scores, ordered by date.
     * The frontend can plot domain_scores over time per domain.
     *
     * Optional query params:
     *   ?limit=10   — number of checks to return (default 10, max 50)
     *   ?domain=Emotional — filter trend data to one domain
     *
     * @param  Request $request
     * @param  User    $youngPerson
     * @return JsonResponse
     */
    public function history(Request $request, User $youngPerson): JsonResponse
    {
        $this->authorize('viewWellbeingHistory', $youngPerson);

        $limit = min((int) $request->input('limit', 10), 50);

        $checks = WellbeingCheck::where('young_person_id', $youngPerson->id)
            ->whereNotNull('completed_at')
            ->with(['domainScores.domain'])
            ->orderByDesc('completed_at')
            ->limit($limit)
            ->get();

        $history = $checks->map(fn($check) => [
            'check_id'            => $check->id,
            'check_type'          => $check->check_type,
            'completed_at'        => $check->completed_at,
            'overall_wb_score'    => $check->overall_wb_score,
            'overall_risk_score'  => $check->overall_risk_score,
            'domain_scores'       => $check->domainScores
                ->when(
                    $request->filled('domain'),
                    fn($c) => $c->filter(fn($ds) => $ds->domain->name === $request->input('domain'))
                )
                ->map(fn($ds) => [
                    'domain'     => $ds->domain->name,
                    'wb_score'   => $ds->average_score,
                    'risk_score' => $ds->risk_score,
                ])
                ->values(),
        ]);

        return response()->json([
            'young_person_id' => $youngPerson->id,
            'check_count'     => $checks->count(),
            'history'         => $history,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // GET /api/wellbeing/{youngPerson}/goal-suggestions
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Returns pending goal suggestions for a young person based on their
     * most recent check's tag patterns and domain scores.
     *
     * The social worker reviews these and approves or dismisses each one.
     * Approved suggestions create a goals row via the GoalController.
     *
     * @param  User $youngPerson
     * @return JsonResponse
     */
    public function goalSuggestions(User $youngPerson): JsonResponse
    {
        $this->authorize('viewWellbeingHistory', $youngPerson);

        $lastCheck = WellbeingCheck::where('young_person_id', $youngPerson->id)
            ->whereNotNull('completed_at')
            ->with(['responses.question.tags'])
            ->orderByDesc('completed_at')
            ->first();

        if (!$lastCheck) {
            return response()->json(['suggestions' => []]);
        }

        // Gather all tags that fired below their trigger threshold in last check
        $suggestions = collect();

        foreach ($lastCheck->responses as $response) {
            foreach ($response->question->tags as $tag) {

                $templates = DB::table('tag_goal_templates')
                    ->join('goal_templates', 'tag_goal_templates.goal_template_id', '=', 'goal_templates.id')
                    ->join('domains', 'goal_templates.source_domain_id', '=', 'domains.id')
                    ->where('tag_goal_templates.tag_id', $tag->id)
                    ->where('tag_goal_templates.trigger_threshold', '>', $response->normalised_score)
                    ->select(
                        'goal_templates.id',
                        'goal_templates.title',
                        'goal_templates.description',
                        'domains.name as domain',
                        'tag_goal_templates.trigger_threshold',
                    )
                    ->get();

                foreach ($templates as $template) {
                    // Key by template ID to deduplicate across responses
                    $suggestions->put($template->id, [
                        'template_id' => $template->id,
                        'title'       => $template->title,
                        'description' => $template->description,
                        'domain'      => $template->domain,
                        'trigger_tag' => $tag->name,
                        'wb_score'    => $response->normalised_score,
                    ]);
                }
            }
        }

        return response()->json([
            'young_person_id' => $youngPerson->id,
            'suggestions'     => $suggestions->values(),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Helper
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Resolves the active open case file ID for a young person.
     * Throws a 404 if no open case file exists — a check cannot be started
     * without an active case.
     */
    private function resolveCaseFileId(User $youngPerson): int
    {
        $caseFile = DB::table('case_files')
            ->where('young_person_id', $youngPerson->id)
            ->where('status', 'open')
            ->orderByDesc('opened_at')
            ->first();

        abort_if(!$caseFile, 404, 'No active case file found for this young person.');

        return $caseFile->id;
    }
}
