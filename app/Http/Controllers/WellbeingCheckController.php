<?php



namespace App\Http\Controllers;



use Illuminate\Http\Request;

use App\Models\Question;

use App\Models\WellbeingCheck;

use App\Services\WellbeingScoringService;

use App\Services\CheckQuestionSelector;



class WellbeingCheckController extends Controller

{

    public function create()

    {

        $child = auth()->user();

        $caseFile = $child->caseFile ?? $child->childCase ?? null;



        if (!$caseFile) {

            return back()->withErrors([

                'wellbeing' => 'No case file is linked to this young person.',

            ]);

        }



        $selector = new CheckQuestionSelector();

        $selectedQuestions = $selector->selectForCase($caseFile->id, 8);



        $questions = $selectedQuestions->groupBy(function ($question) {

            return $question->domain->name ?? 'General';

        });



        return view('child.wellbeing.check', compact('questions'));

    }



    public function submit(Request $request)

    {

        $child = auth()->user();

        $caseFile = $child->caseFile ?? $child->childCase ?? null;



        if (!$caseFile) {

            return back()->withErrors([

                'wellbeing' => 'No case file is linked to this young person.',

            ]);

        }



        $questionIds = collect($request->all())

            ->keys()

            ->filter(fn ($key) => str_starts_with($key, 'question_'))

            ->map(fn ($key) => (int) str_replace('question_', '', $key))

            ->values();



        if ($questionIds->isEmpty()) {

            return back()->withErrors([

                'wellbeing' => 'No wellbeing answers were submitted.',

            ])->withInput();

        }



        $questions = Question::whereIn('id', $questionIds)->get()->keyBy('id');



        $check = WellbeingCheck::create([

            'case_file_id' => $caseFile->id,

            'completed_by_type' => 'child',

            'completed_by_user_id' => $child->id,

            'week_start' => now()->startOfWeek(),

        ]);



        $scoring = new WellbeingScoringService();



        foreach ($questionIds as $questionId) {

            $question = $questions->get($questionId);



            if (!$question) {

                continue;

            }



            $raw = $request->input("question_{$question->id}");



            if ($raw === null) {

                continue;

            }



            $score = $scoring->normalise(

                $raw,

                $question->min_value,

                $question->max_value,

                $question->is_positive

            );



            $risk = $scoring->riskContribution(

                $score,

                $question->risk_weight

            );



            $check->responses()->create([

                'question_id' => $question->id,

                'raw_value' => $raw,

                'normalized_score' => $score,

                'risk_score' => $risk,

            ]);

        }



        $scoring->calculateDomainScores($check);



        $overallScore = $scoring->overallFromDomains($check);

        $overallRisk = $scoring->overallRiskFromDomains($check);

        $safeguarding = $scoring->analyseTagPatterns($check);

        $riskLevel = $scoring->classifyRisk($overallRisk);



        if ($safeguarding) {

            $riskLevel = 'critical';

        }



        $check->update([

            'overall_score' => $overallScore,

            'overall_risk_score' => $overallRisk,

            'risk_level' => $riskLevel,

        ]);



        return redirect()->route('child.dashboard')

            ->with('success', 'Wellbeing check submitted successfully!');

    }



    public function result(WellbeingCheck $check)

    {

        $check->load([

            'domainScores.domain',

            'responses.question.tags',

        ]);



        return view('child.wellbeing.result', compact('check'));

    }



    public function alerts()

    {

        $user = auth()->user();



        abort_if($user->role !== 'social_worker', 403);



        $checks = WellbeingCheck::with([

                'caseFile',

                'domainScores.domain',

            ])

            ->whereHas('caseFile.users', function ($query) use ($user) {

                $query->where('users.id', $user->id)

                      ->where('case_user.role', 'social_worker');

            })

            ->where(function ($query) {

                $query->where('overall_score', '<', 50)

                      ->orWhere('safeguarding_flag', true);

            })

            ->orderByDesc('week_start')

            ->get();



        return view('socialworker.wellbeing.alerts', compact('checks'));

    }



    public function store(Request $request)

    {

        $data = $request->validate([

            'case_file_id' => 'required|exists:case_files,id',

            'emotional_score' => 'required|integer|min:1|max:5',

            'behavioural_score' => 'required|integer|min:1|max:5',

            'physical_score' => 'required|integer|min:1|max:5',

            'safety_score' => 'required|integer|min:1|max:5',

            'school_score' => 'required|integer|min:1|max:5',

            'relationship_score' => 'required|integer|min:1|max:5',

            'journal_notes' => 'nullable|string',

        ]);



        $data['overall_score'] = (

            $data['emotional_score'] +

            $data['behavioural_score'] +

            $data['physical_score'] +

            $data['safety_score'] +

            $data['school_score'] +

            $data['relationship_score']

        ) / 6;



        WellbeingCheck::create($data);



        return redirect()->route('child.dashboard')

            ->with('success', 'Wellbeing check submitted successfully!');

    }

}