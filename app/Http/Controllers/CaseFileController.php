<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\CaseFile;
use App\Models\User;

class CaseFileController extends Controller
{

    public function index()
    {
        $user = auth()->user();

        $cases = $user->socialWorkerCases()->with([
            'youngPerson',
            'wellbeingChecks' => fn($q) => $q->whereNotNull('completed_at')
                                             ->orderByDesc('completed_at'),
        ])->get();

        return view('socialworker.cases.index', compact('cases'));
    }

public function show(CaseFile $case)
{
    $user = auth()->user();

    abort_if(! $case->users()->where('users.id', $user->id)->exists(), 403);

    $case->load([
        'youngPerson',
        'carers',
        'appointments'      => fn ($q) => $q->orderBy('start_time', 'desc'),
        'placements.carer',
        'medicalInfos',
        'educationInfos',
        'documents',
        'wellbeingChecks.domainScores.domain',
        'wellbeingChecks.submittedBy', 
    ]);

    $case->wellbeingChecks->each(function ($check) {
        $scoresByDomain = $check->domainScores
            ->keyBy(fn($s) => strtolower(trim($s->domain->name ?? '')));

        $check->emotional_score         = round($scoresByDomain['emotional']?->average_score ?? 0, 1);
        $check->behavioural_score       = round($scoresByDomain['behavioural']?->average_score ?? 0, 1);
        $check->social_score            = round($scoresByDomain['social']?->average_score ?? 0, 1);
        $check->physical_score          = round($scoresByDomain['physical']?->average_score ?? 0, 1);
        $check->education_score         = round($scoresByDomain['education']?->average_score ?? 0, 1);
        $check->safety_score            = round($scoresByDomain['safety']?->average_score ?? 0, 1);
        $check->life_satisfaction_score = round($scoresByDomain['life satisfaction']?->average_score ?? 0, 1);
    });

    $tab             = request('tab', 'personal');
    $checkId         = request('check');
    $availableCarers = User::where('role', 'carer')->orderBy('name')->get(['id', 'name']);

    $pendingGoals = DB::table('case_goals')
        ->join('goals', 'case_goals.goal_id', '=', 'goals.id')
        ->leftJoin('domains', 'goals.source_domain_id', '=', 'domains.id')
        ->where('case_goals.case_file_id', $case->id)
        ->where('case_goals.status', 'pending')
        ->select(
            'case_goals.id as case_goal_id',
            'case_goals.source_check_id',
            'goals.title',
            'goals.description',
            'goals.suggested_at',
            'goals.template_id',
            'domains.name as domain_name',
        )
        ->get();

    $pendingGoals->transform(function ($goal) {
        if (!$goal->template_id) return $goal;
        $tags = DB::table('tag_goal_templates')
            ->join('tags', 'tag_goal_templates.tag_id', '=', 'tags.id')
            ->where('tag_goal_templates.goal_template_id', $goal->template_id)
            ->pluck('tags.name');
        $goal->triggered_by_tags = $tags->join(', ');
        return $goal;
    });

    $activeGoals = DB::table('case_goals')
        ->join('goals', 'case_goals.goal_id', '=', 'goals.id')
        ->leftJoin('domains', 'goals.source_domain_id', '=', 'domains.id')
        ->where('case_goals.case_file_id', $case->id)
        ->where('case_goals.status', 'in_progress')
        ->select(
            'case_goals.id as case_goal_id',
            'case_goals.due_date',
            'case_goals.child_accepted_at',
            'goals.title',
            'goals.description',
            'domains.name as domain_name',
        )
        ->get();

    $completedGoals = DB::table('case_goals')
        ->join('goals', 'case_goals.goal_id', '=', 'goals.id')
        ->leftJoin('domains', 'goals.source_domain_id', '=', 'domains.id')
        ->where('case_goals.case_file_id', $case->id)
        ->where('case_goals.status', 'completed')
        ->select(
            'case_goals.id as case_goal_id',
            'case_goals.updated_at as completed_at',
            'goals.title',
            'domains.name as domain_name',
        )
        ->orderByDesc('case_goals.updated_at')
        ->get();

    $caseGoalIds = $activeGoals->pluck('case_goal_id');

    $tasksByCaseGoal = DB::table('tasks')
        ->whereIn('case_goal_id', $caseGoalIds)
        ->where('child_visible', true)
        ->select('id', 'case_goal_id', 'title', 'description', 'completed_at', 'ai_suggested', 'child_visible')
        ->get()
        ->groupBy('case_goal_id');

    $pendingTasksByCaseGoal = DB::table('tasks')
        ->whereIn('case_goal_id', $caseGoalIds)
        ->where('child_visible', false)
        ->select('id', 'case_goal_id', 'title', 'description', 'ai_suggested')
        ->get()
        ->groupBy('case_goal_id');
    $domains = DB::table('domains')->orderBy('name')->get();

    return view('socialworker.cases.show', compact(
        'case', 'availableCarers', 'tab', 'checkId',
        'pendingGoals', 'activeGoals', 'completedGoals',
        'tasksByCaseGoal', 'pendingTasksByCaseGoal', 'domains'
    ));
}

    public function edit(CaseFile $case)
    {
        $children = User::where('role', 'young_person')->orderBy('name')->get();
        $carers   = User::where('role', 'carer')->orderBy('name')->get();

        return view('socialworker.case_edit', compact('case', 'children', 'carers'));
    }


    public function update(Request $request, CaseFile $case)
    {
        $validated = $request->validate([
            'young_person_id' => 'nullable|exists:users,id',
            'status'          => 'required|string|max:100',
            'risk_level'      => 'required|string|max:50',
            'carers'          => 'nullable|array',
            'carers.*'        => 'exists:users,id',
        ]);

        // Capture old values before update for change detection
        $oldStatus    = $case->status;
        $oldRiskLevel = $case->risk_level;

        $case->update([
            'young_person_id' => $validated['young_person_id'] ?? null,
            'status'          => $validated['status'],
            'risk_level'      => $validated['risk_level'],
        ]);

        if (!empty($validated['carers'])) {
            $case->users()->syncWithPivotValues(
                $validated['carers'],
                ['role' => 'carer', 'assigned_at' => now()],
                false
            );
        }

        // Notify carers of meaningful changes
        if ($oldRiskLevel !== $validated['risk_level']) {
            $this->notifyCarersOfCaseChange($case, 'risk_level', $oldRiskLevel, $validated['risk_level']);
        }
        if ($oldStatus !== $validated['status']) {
            $this->notifyCarersOfCaseChange($case, 'status', $oldStatus, $validated['status']);
        }

        return redirect()
            ->route('socialworker.cases.show', $case)
            ->with('success', 'Case updated successfully.');
    }

    private function notifyCarersOfCaseChange(CaseFile $case, string $field, string $oldVal, string $newVal): void
    {
        $summary = match($field) {
            'risk_level' => 'Case risk level changed from ' . ucfirst($oldVal) . ' to ' . ucfirst($newVal),
            'status'     => 'Case status changed from ' . ucfirst($oldVal) . ' to ' . ucfirst($newVal),
            default      => 'Case updated',
        };

        \App\Models\User::whereIn('id',
            \Illuminate\Support\Facades\DB::table('case_user')
                ->where('case_file_id', $case->id)
                ->where('role', 'carer')
                ->pluck('user_id')
        )->get()->each(fn($carer) => $carer->notify(
            new \App\Notifications\CareHubNotification(
                type:    'case_updated',
                summary: $summary,
                data:    ['case_file_id' => $case->id],
            )
        ));
    }


    public function assignCarer(Request $request, CaseFile $case)
    {
        abort_if(auth()->user()->role !== 'social_worker', 403);

        $validated = $request->validate([
            'carer_id' => 'required|exists:users,id',
        ]);

        $case->users()->syncWithoutDetaching([
            $validated['carer_id'] => [
                'role'        => 'carer',
                'assigned_at' => now(),
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);

        $case->load('carers');

        return redirect()->back()->with('success', 'Carer assigned successfully.');
    }


    public function storePlacement(Request $request, CaseFile $case)
    {
        $validated = $request->validate([
            'type'       => 'required|string|max:100',
            'location'   => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
            'carer_id'   => 'nullable|exists:users,id',
            'capacity'   => 'nullable|integer|min:1',
            'notes'      => 'nullable|string',
        ]);

        // Attempt to geocode the location string via Nominatim.
        // Nominatim requires a descriptive User-Agent per usage policy.
        $lat = null;
        $lng = null;

        try {
            $encoded  = urlencode($validated['location']);
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'User-Agent' => 'CareHub/1.0 (carehub@localhost)',
            ])->get("https://nominatim.openstreetmap.org/search", [
                'q'              => $validated['location'],
                'format'         => 'json',
                'limit'          => 1,
                'addressdetails' => 0,
            ]);

            if ($response->successful()) {
                $results = $response->json();
                if (! empty($results)) {
                    $lat = (float) $results[0]['lat'];
                    $lng = (float) $results[0]['lon'];
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Placement geocoding failed', [
                'location' => $validated['location'],
                'error'    => $e->getMessage(),
            ]);
        }

        $case->placements()->create([
            'type'              => $validated['type'],
            'location'          => $validated['location'],
            'start_date'        => $validated['start_date'],
            'end_date'          => $validated['end_date'] ?? null,
            'carer_id'          => $validated['carer_id'] ?? null,
            'capacity'          => $validated['capacity'] ?? 1,
            'current_occupancy' => 0,
            'status'            => 'active',
            'notes'             => $validated['notes'] ?? null,
            'latitude'          => $lat,
            'longitude'         => $lng,
        ]);

        return back()->with('success', 'Placement added successfully.');
    }


    public function storeMedical(Request $request, CaseFile $case)
    {
        $validated = $request->validate([
            'condition' => 'required|string|max:255',
            'notes'     => 'nullable|string',
        ]);

        $case->medicalInfos()->create($validated);

        return back()->with('success', 'Medical record added.');
    }


    public function storeEducation(Request $request, CaseFile $case)
    {
        $validated = $request->validate([
            'school_name' => 'required|string|max:255',
            'grade'       => 'nullable|string|max:50',
            'notes'       => 'nullable|string',
        ]);

        $case->educationInfos()->create($validated);

        return back()->with('success', 'Education record added.');
    }

    public function storeDocument(Request $request, CaseFile $case)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png,docx|max:10240',
        ]);

        $path = $request->file('file')->store('documents', 'public');

        $case->documents()->create([
            'name'        => $validated['name'],
            'file_path'   => $path,
            'uploaded_by' => auth()->id(),
        ]);

        return back()->with('success', 'Document uploaded successfully.');
    }

    public function markReviewed(CaseFile $case)
    {
        abort_if(
            ! $case->users()->where('users.id', auth()->id())->exists(),
            403
        );
    
        $case->update(['last_reviewed_at' => now()]);
    
        return redirect()->back()->with('success', 'Case marked as reviewed.');
    }
    
}
