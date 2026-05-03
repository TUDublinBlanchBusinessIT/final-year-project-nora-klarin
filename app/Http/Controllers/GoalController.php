<?php
namespace App\Http\Controllers;

use App\Models\CaseFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GoalController extends Controller
{
    // List all goals for a case — pending suggestions + active + completed
    public function index(CaseFile $case)
    {
        abort_if(!$case->users()->where('users.id', auth()->id())->exists(), 403);

        $goals = DB::table('case_goals')
            ->join('goals', 'case_goals.goal_id', '=', 'goals.id')
            ->leftJoin('goal_templates', 'goals.template_id', '=', 'goal_templates.id')
            ->leftJoin('domains', 'goals.source_domain_id', '=', 'domains.id')
            ->leftJoin('users as approver', 'goals.approved_by', '=', 'approver.id')
            ->where('case_goals.case_file_id', $case->id)
            ->select(
                'case_goals.id as case_goal_id',
                'case_goals.status',
                'case_goals.due_date',
                'case_goals.child_visible',
                'case_goals.child_accepted_at',
                'case_goals.source_check_id',
                'goals.id as goal_id',
                'goals.title',
                'goals.description',
                'goals.suggested_at',
                'domains.name as domain_name',
                'approver.name as approved_by_name',
            )
            ->orderByRaw("FIELD(case_goals.status, 'pending', 'in_progress', 'completed')")
            ->get();

        return view('socialworker.goals.index', compact('case', 'goals'));
    }

    // Approve a suggested goal — makes it visible to child
    public function approve(Request $request, int $caseGoalId)
    {
        $caseGoal = DB::table('case_goals')->find($caseGoalId);
        abort_if(!$caseGoal, 404);

        $validated = $request->validate([
            'due_date' => 'nullable|date|after:today',
        ]);

        DB::table('goals')
            ->where('id', $caseGoal->goal_id)
            ->update([
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'updated_at'  => now(),
            ]);

        DB::table('case_goals')
            ->where('id', $caseGoalId)
            ->update([
                'status'        => 'in_progress',
                'child_visible' => 1,
                'due_date'      => $validated['due_date'] ?? null,
                'updated_at'    => now(),
            ]);
        if (config('services.anthropic.key')) {
        dispatch(new \App\Jobs\SuggestTasksForGoal($caseGoalId, $caseGoal->goal_id));
    }
        return back()->with('success', 'Goal approved and sent to child.');
    }

    // Social worker creates a goal manually (not from a suggestion)
    public function store(Request $request, CaseFile $case)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'domain_id'   => 'nullable|exists:domains,id',
            'due_date'    => 'nullable|date|after:today',
        ]);

        $goalId = DB::table('goals')->insertGetId([
            'title'            => $validated['title'],
            'description'      => $validated['description'] ?? null,
            'source_domain_id' => $validated['domain_id'] ?? null,
            'approved_by'      => auth()->id(),
            'approved_at'      => now(),
            'suggested_at'     => now(),
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);

        DB::table('case_goals')->insert([
            'case_file_id'  => $case->id,
            'goal_id'       => $goalId,
            'status'        => 'in_progress',
            'child_visible' => 1,
            'due_date'      => $validated['due_date'] ?? null,
            'suggested_by'  => auth()->id(),
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        return back()->with('success', 'Goal created.');
    }

    // Add a task to a goal
    public function addTask(Request $request, int $caseGoalId)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $taskId = DB::table('tasks')->insertGetId([
            'title'        => $validated['title'],
            'description'  => $validated['description'] ?? null,
            'case_goal_id' => $caseGoalId,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        $caseGoal = DB::table('case_goals')->find($caseGoalId);
        DB::table('task_goal')->insert([
            'goal_id'    => $caseGoal->goal_id,
            'task_id'    => $taskId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Task added.');
    }

    // Mark a goal complete
    public function complete(int $caseGoalId)
    {
        DB::table('case_goals')
            ->where('id', $caseGoalId)
            ->update(['status' => 'completed', 'updated_at' => now()]);

        return back()->with('success', 'Goal marked complete.');
    }
}