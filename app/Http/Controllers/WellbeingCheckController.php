<?php



namespace App\Http\Controllers;

use App\Models\WellbeingCheck;
use App\Services\CheckQuestionSelector;
use App\Services\WellbeingAlertService;
use App\Services\WellbeingScoringService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\CaseFile;
use App\Models\User;
use App\Models\Question;
use App\Models\Domain;


class WellbeingCheckController extends Controller

{
    public function __construct(
        private readonly CheckQuestionSelector   $selector,
        private readonly WellbeingScoringService $scoringService,
        private readonly WellbeingAlertService   $alertService,
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

        $caseFileId = DB::table('case_files')
            ->where('young_person_id', $youngPerson->id)
            ->where('status', 'open')
            ->orderByDesc('created_at')
            ->value('id');

        abort_if(!$caseFileId, 404, 'No active case file found.');

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

        $scoring = new WellbeingScoringService();


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
        if ($check->young_person_id !== Auth::id()) {
            abort(403);
        }

        if ($check->completed_at !== null) {
            return response()->json(['message' => 'Already submitted.'], 409);
        }

        $request->validate([
            'responses'                => ['required', 'array', 'min:1'],
            'responses.*.question_id'  => ['required', 'integer'],
            'responses.*.raw_value'    => ['required', 'integer'],
        ]);

        $result = DB::transaction(function () use ($request, $check) {

            foreach ($request->input('responses') as $r) {
                $check->responses()->create([
                    'question_id'       => $r['question_id'],
                    'raw_value'         => $r['raw_value'],
                    'normalised_score'  => 0,
                    'risk_contribution' => 0,
                ]);
            }

            $summary = $this->scoringService->process($check);

            $alerts = $this->alertService->evaluate($check, $summary);

            $check->update([
            'completed_at' => now(),
            'risk_level'   => $summary['risk_classification'],
]);

            return [$summary, $alerts];
        });

        [$summary, $alerts] = $result;

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
            'alerts_generated'  => $alerts->count(),
            'safeguarding_flag' => $summary['safeguarding_triggered'],
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
}



