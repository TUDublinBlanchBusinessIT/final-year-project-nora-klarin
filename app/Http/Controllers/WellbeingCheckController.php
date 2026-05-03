<?php



namespace App\Http\Controllers;

use App\Models\WellbeingCheck;
use App\Services\CheckQuestionSelector;
use App\Services\WellbeingCheckProcessor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\CaseFile;
use App\Models\User;
use App\Models\Alert;
use App\Models\Question;
use App\Models\Domain;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class WellbeingCheckController extends Controller

{
    use AuthorizesRequests;

    public function __construct(
        private readonly CheckQuestionSelector   $selector,
        private readonly WellbeingCheckProcessor  $processor,
    ) {}


    public function index()
    {
        return view('child.wellbeing.check', [
            'questions' => collect(),
        ]);
    }

    public function start(Request $request): JsonResponse
    {
        /** @var \App\Models\User $youngPerson */
        $youngPerson = Auth::user();

        $this->authorize('startWellbeingCheck', $youngPerson);
        return $this->beginCheckFor($youngPerson);
    }

    public function startForYoungPerson(Request $request, User $youngPerson): JsonResponse
    {
        $this->authorize('startWellbeingCheck', $youngPerson);

        return $this->beginCheckFor($youngPerson);
    }

    public function create()
    {
        return $this->index();
    }

    protected function beginCheckFor(User $youngPerson): JsonResponse
    {
        $caseFileId = CaseFile::where('young_person_id', $youngPerson->id)
            ->where('status', 'open')
            ->latest()
            ->value('id');

        $isIntake = !WellbeingCheck::where('young_person_id', $youngPerson->id)
            ->whereNotNull('completed_at')
            ->exists();

        $check = WellbeingCheck::create([
            'young_person_id' => $youngPerson->id,
            'case_file_id'    => $caseFileId,
            'check_type'      => $isIntake ? 'intake' : 'scheduled',
            'game_mode'       => 'slider',
        ]);

        $questions = $this->selector->selectFor($youngPerson);
        foreach ($questions as $question) {
            DB::table('check_question_log')->insert([
                'wellbeing_check_id' => $check->id,
                'question_id'        => $question->id,
                'created_at'         => now(),
            ]);
            
        }

        return response()->json([
            'check_id'   => $check->id,
            'check_type' => $check->check_type,
            'questions'  => $questions->map(fn($q) => [
                'id'            => $q->id,
                'domain'        => $q->domain_name,
                'text'          => $q->text,
                'response_type' => $q->response_type,
                'min_value'     => $q->min_value,
                'max_value'     => $q->max_value,
                'option_labels' => $q->option_labels,
            ]),
        ], 201);
    }


    public function submitCheck(Request $request, WellbeingCheck $check): JsonResponse
    {
        $this->authorize('submitWellbeingCheck', $check);

        if ($check->completed_at !== null) {
            return response()->json(['message' => 'Already submitted.'], 409);
        }
        
        $request->validate([
            'responses'                => ['required', 'array', 'min:1'],
            'responses.*.question_id'  => ['required', 'integer'],
            'responses.*.raw_value'    => ['required', 'integer'],
        ]);

        $summary = $this->processor->submitResponses(
            $check,
            $request->input('responses'),
            $request->user(),
        );
        app(\App\Services\GoalSuggestionService::class)
        ->suggestFromCheck($check);

        return response()->json([
            'message'             => 'Check submitted successfully.',
            'overall_wb_score'    => $summary['overall_wb_score'],
            'overall_risk_score'  => $summary['overall_risk_score'],
            'risk_classification' => $summary['risk_classification'],
            'domain_scores'       => $summary['domain_scores']->map(fn($ds) => [
                'domain'     => $ds->domain->name,
                'wb_score'   => $ds->average_score,
                'risk_score' => $ds->risk_score,
            ]),
            'alerts_generated'    => null,
            'safeguarding_flag'   => $summary['safeguarding_triggered'],
        ]);
    }
    public function result(WellbeingCheck $check)
    {
        $check->load('domainScores.domain');
        return view('child.wellbeing.result', compact('check'));
    }

    public function alerts()
    {
        $user = auth()->user();
        abort_if($user->role !== 'social_worker', 403);

        $checks = WellbeingCheck::with('youngPerson')
            ->whereIn('risk_level', ['high', 'critical'])
            ->orderByDesc('completed_at')
            ->get()
            ->groupBy('young_person_id');

        return view('socialworker.wellbeing.alerts', compact('checks'));
    }

    public function getDetails(WellbeingCheck $check): JsonResponse
    {
        $user = auth()->user();
        abort_if($user->role !== 'social_worker', 403);

        // Check if user has access to this check via case assignment
        abort_if(!$check->caseFile->users()->where('users.id', $user->id)->exists(), 403);
        
        $check->load(['domainScores.domain', 'submittedBy', 'alerts']);

        // Calculate domain changes compared to previous check
        $previousCheck = WellbeingCheck::where('young_person_id', $check->young_person_id)
            ->where('id', '<', $check->id)
            ->orderBy('id', 'desc')
            ->first();

        $domainChanges = [];
        if ($previousCheck) {
            $previousCheck->load('domainScores.domain');

            $currentDomains = $check->domainScores->keyBy('domain.name');
            $previousDomains = $previousCheck->domainScores->keyBy('domain.name');

            foreach ($currentDomains as $domainName => $currentScore) {
                if (isset($previousDomains[$domainName])) {
                    $change = round($currentScore->average_score - $previousDomains[$domainName]->average_score, 1);
                    if ($change !== 0) {
                        $domainChanges[] = [
                            'domain' => $domainName,
                            'change' => $change,
                            'current' => $currentScore->average_score,
                            'previous' => $previousDomains[$domainName]->average_score,
                        ];
                    }
                }
            }
        }

        return response()->json([
            'id' => $check->id,
            'created_at_formatted' => $check->created_at->format('d M Y H:i'),
            'overall_score' => round($check->overall_score, 1),
            'risk_level' => $check->risk_level,
            'risk_level_capitalized' => ucfirst($check->risk_level),
            'submitted_by_name' => $check->submittedBy?->name,
            'emotional_score' => $check->emotional_score ? round($check->emotional_score, 1) : null,
            'behavioural_score' => $check->behavioural_score ? round($check->behavioural_score, 1) : null,
            'social_score' => $check->social_score ? round($check->social_score, 1) : null,
            'physical_score' => $check->physical_score ? round($check->physical_score, 1) : null,
            'education_score' => $check->education_score ? round($check->education_score, 1) : null,
            'safety_score' => $check->safety_score ? round($check->safety_score, 1) : null,
            'life_satisfaction_score' => $check->life_satisfaction_score ? round($check->life_satisfaction_score, 1) : null,
            'domain_changes' => $domainChanges,
            'alerts' => $check->alerts->map(function ($alert) {
                $title = match($alert->alert_type) {
                    'tag_override' => 'Safeguarding Alert',
                    'critical_response' => 'Critical Response',
                    'domain_drop' => 'Domain Score Alert',
                    'domain_decline' => 'Domain Decline Alert',
                    default => 'Alert'
                };

                return [
                    'id' => $alert->id,
                    'title' => $title,
                    'description' => $alert->message,
                    'severity' => $alert->severity,
                    'type' => $alert->alert_type,
                ];
            }),
        ]);
    }
}



