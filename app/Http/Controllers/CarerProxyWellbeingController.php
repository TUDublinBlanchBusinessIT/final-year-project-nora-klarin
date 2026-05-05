<?php

namespace App\Http\Controllers;

use App\Models\CaseFile;
use App\Models\WellbeingCheck;
use App\Models\User;
use App\Notifications\CareHubNotification;
use App\Services\WellbeingCheckProcessor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CarerProxyWellbeingController extends Controller
{
    
    private const PROXY_QUESTIONS = [
        'emotional' => [
            'text'         => "How would you describe this child's general mood over the past week?",
            'option_labels' => ['Very low', 'Mostly low', 'Mixed', 'Mostly positive', 'Very positive'],
        ],
        'behavioural' => [
            'text'          => "How has the child's behaviour been at home over the past week?",
            'option_labels' => ['Very difficult', 'Frequent difficulties', 'Some difficulties', 'Mostly settled', 'Very settled'],
        ],
        'social' => [
            'text'          => "How well is the child interacting with others (family, peers)?",
            'option_labels' => ['Withdrawn', 'Poor', 'Some difficulty', 'Well', 'Very well'],
        ],
        'physical' => [
            'text'          => "How would you describe the child's sleep, appetite, and physical health this week?",
            'option_labels' => ['Very poor', 'Poor', 'Fair', 'Good', 'Very good'],
        ],
        'education' => [
            'text'          => "Is the child attending school or following their daily routine?",
            'option_labels' => ['Not attending', 'Frequent absences', 'Some absences', 'Mostly attending', 'Full attendance'],
        ],
        'safety' => [
            'text'          => "Do you have any concerns about the child's safety or wellbeing at home?",
            'option_labels' => ['Immediate concern', 'Significant concern', 'Moderate concern', 'Minor concern', 'No concerns'],
        ],
        'life satisfaction' => [
            'text'          => "How does the child seem to feel about their life overall?",
            'option_labels' => ['Very unhappy', 'Mostly unhappy', 'Mixed', 'Mostly happy', 'Very happy'],
        ],
    ];

    public function __construct(private readonly WellbeingCheckProcessor $processor) {}

    /**
     * Show the carer proxy wellbeing form for a specific case.
     */
    public function create(CaseFile $case)
    {
        $user = Auth::user();
        abort_if($user->role !== 'carer', 403);
        abort_if(
            !$case->users()->where('users.id', $user->id)->where('case_user.role', 'carer')->exists(),
            403
        );

        // Check if there's already a pending (incomplete) proxy check for this case
        $pendingCheck = WellbeingCheck::where('case_file_id', $case->id)
            ->where('check_type', 'carer_proxy')
            ->whereNull('completed_at')
            ->latest()
            ->first();

        return view('carer.wellbeing.proxy_form', [
            'case'         => $case,
            'pendingCheck' => $pendingCheck,
            'questions'    => self::PROXY_QUESTIONS,
        ]);
    }

    /**
     * Create the check, log questions, map Likert answers, run scoring pipeline.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        abort_if($user->role !== 'carer', 403);

        $request->validate([
            'case_file_id' => 'required|exists:case_files,id',
            'responses'    => 'required|array|min:7',
            'responses.*'  => 'required|integer|min:0|max:4',
            'notes'        => 'nullable|string|max:2000',
        ]);

        $case = CaseFile::findOrFail($request->case_file_id);

        abort_if(
            !$case->users()->where('users.id', $user->id)->where('case_user.role', 'carer')->exists(),
            403
        );

        // Resolve domain → question ID mapping from the questions table
        // Proxy questions are seeded with source_framework = 'carer_proxy'
        $proxyQuestions = DB::table('questions')
            ->join('domains', 'questions.domain_id', '=', 'domains.id')
            ->where('questions.source_framework', 'carer_proxy')
            ->where('questions.is_active', 1)
            ->select('questions.id', DB::raw('LOWER(domains.name) as domain_name'))
            ->get()
            ->keyBy('domain_name');

        // Validate all 7 domains have a seeded question
        $missing = collect(array_keys(self::PROXY_QUESTIONS))->diff($proxyQuestions->keys());
        if ($missing->isNotEmpty()) {
            return back()->withErrors([
                'responses' => 'Proxy questions not found for domains: ' . $missing->join(', ') .
                               '. Run: php artisan db:seed --class=CarerProxyQuestionSeeder',
            ]);
        }

        // Create the wellbeing check
        $check = WellbeingCheck::create([
            'young_person_id' => $case->young_person_id,
            'case_file_id'    => $case->id,
            'check_type'      => 'carer_proxy',
            'game_mode'       => 'likert_5',
            'submitted_by'    => $user->id,
        ]);

        // Log questions to check_question_log (required by validateQuestionMembership)
        $logRows = $proxyQuestions->map(fn($q) => [
            'wellbeing_check_id' => $check->id,
            'question_id'        => $q->id,
            'created_at'         => now(),
        ])->values()->toArray();

        DB::table('check_question_log')->insert($logRows);

        // Map submitted responses: domain_name → raw_value (0-4)
        $responses = collect($request->input('responses'))
            ->map(function ($rawValue, $domainName) use ($proxyQuestions) {
                $question = $proxyQuestions->get(strtolower($domainName));
                if (!$question) return null;
                return [
                    'question_id' => $question->id,
                    'raw_value'   => (int) $rawValue,
                ];
            })
            ->filter()
            ->values()
            ->toArray();

        // Run the full scoring + alert + goal pipeline
        $summary = $this->processor->submitResponses($check, $responses, $user);

        // Store carer notes as a diary-style entry if provided
        if ($request->filled('notes')) {
            DB::table('diary_entries')->insert([
                'user_id'    => $user->id,
                'title'      => 'Proxy wellbeing update — ' . now()->format('d M Y'),
                'content'    => $request->input('notes'),
                'mood'       => null,
                'private'    => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Notify assigned social workers
        $swIds = DB::table('case_user')
            ->where('case_file_id', $case->id)
            ->where('role', 'social_worker')
            ->pluck('user_id');

        \App\Models\User::whereIn('id', $swIds)->get()->each(fn($sw) =>
            $sw->notify(new CareHubNotification(
                type:    'wellbeing_completed',
                summary: 'Carer proxy wellbeing update submitted for ' .
                         ($case->youngPerson->name ?? 'young person') .
                         '. Overall score: ' . round($summary['overall_score']) . '/100.',
                data:    ['case_file_id' => $case->id],
            ))
        );

        return redirect()
            ->route('carer.cases.show', $case)
            ->with('success', 'Wellbeing update submitted. The social worker has been notified.');
    }
}
