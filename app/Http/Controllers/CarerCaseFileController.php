<?php

namespace App\Http\Controllers;

use App\Models\CaseFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CarerCaseFileController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $cases = CaseFile::whereHas('users', function ($q) use ($user) {
            $q->where('case_user.user_id', $user->id)
              ->where('case_user.role', 'carer');
        })->with([
            'youngPerson',
            'placements',
            'appointments',
            'wellbeingChecks',
        ])->where('status', 'open')->get();

        return view('carer.cases.index', compact('cases'));
    }

    public function show(CaseFile $case)
    {
        $user = Auth::user();

        abort_if(
            !$case->users()->where('users.id', $user->id)->where('case_user.role', 'carer')->exists(),
            403
        );

        $case->load([
            'youngPerson',
            'users',
            'placements',
            'medicalInfos',
            'educationInfos',
            'documents.uploadedBy',
            'appointments',
            'wellbeingChecks.domainScores.domain',
        ]);

        // ── Goals visible to the child ────────────────────────────────────────
        // Only in_progress goals that have been published (child_visible = 1)
        $activeGoals = DB::table('case_goals')
            ->join('goals', 'case_goals.goal_id', '=', 'goals.id')
            ->leftJoin('domains', 'goals.source_domain_id', '=', 'domains.id')
            ->where('case_goals.case_file_id', $case->id)
            ->where('case_goals.status', 'in_progress')
            ->where('case_goals.child_visible', 1)
            ->select(
                'case_goals.id as case_goal_id',
                'case_goals.due_date',
                'case_goals.child_accepted_at',
                'case_goals.status',
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

        // ── Tasks: only published (child_visible = true) ──────────────────────
        $caseGoalIds = $activeGoals->pluck('case_goal_id');

        $tasksByCaseGoal = DB::table('tasks')
            ->whereIn('case_goal_id', $caseGoalIds)
            ->where('child_visible', true)
            ->select('id', 'case_goal_id', 'title', 'description', 'completed_at', 'ai_suggested')
            ->get()
            ->groupBy('case_goal_id');

        // ── Latest wellbeing check summary ────────────────────────────────────
        $latestCheck = $case->wellbeingChecks
            ->filter(fn($c) => $c->completed_at !== null)
            ->sortByDesc('completed_at')
            ->first();

        return view('carer.cases.show', compact(
            'case',
            'activeGoals',
            'completedGoals',
            'tasksByCaseGoal',
            'latestCheck',
        ));
    }

    public function storeDocument(Request $request, CaseFile $case)
    {
        $user = Auth::user();

        abort_if(
            !$case->users()->where('users.id', $user->id)->where('case_user.role', 'carer')->exists(),
            403
        );

        $request->validate([
            'name' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png,docx|max:10240',
        ]);

        $path = $request->file('file')->store('documents', 'public');

        $case->documents()->create([
            'name'        => $request->name,
            'file_path'   => $path,
            'uploaded_by' => $user->id,
        ]);

        $uploaderName = $user->name;
        \App\Models\User::whereIn('id',
            DB::table('case_user')
                ->where('case_file_id', $case->id)
                ->where('role', 'social_worker')
                ->pluck('user_id')
        )->get()->each(fn($sw) => $sw->notify(new \App\Notifications\CareHubNotification(
            type:    'document_uploaded',
            summary: $uploaderName . ' uploaded "' . $request->name . '"',
            data:    ['case_file_id' => $case->id],
        )));

        return back()->with('success', 'Document uploaded.');
    }
}
