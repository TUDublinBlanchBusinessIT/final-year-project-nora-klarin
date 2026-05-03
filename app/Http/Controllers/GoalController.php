<?php
namespace App\Http\Controllers;

use App\Models\CaseFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GoalController extends Controller
{
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

    public function approve(Request $request, int $caseGoalId)
    {
        $caseGoal = DB::table('case_goals')->find($caseGoalId);
        abort_if(!$caseGoal, 404);

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date'    => 'nullable|date|after:today',
        ]);

        // Update the goal with the SW's reframed wording
        DB::table('goals')
            ->where('id', $caseGoal->goal_id)
            ->update([
                'title'       => $validated['title'],
                'description' => $validated['description'] ?? null,
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

        if (config('services.gemini.key')) {
            dispatch(new \App\Jobs\SuggestTasksForGoal($caseGoalId, $caseGoal->goal_id));
        }

        return back()->with('success', 'Goal approved and sent to child.');
    }

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
            'ai_suggested'  => false,
            'child_visible' => true,
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

    public function complete(int $caseGoalId)
    {
        DB::table('case_goals')
            ->where('id', $caseGoalId)
            ->update(['status' => 'completed', 'updated_at' => now()]);

        return back()->with('success', 'Goal marked complete.');
    }

    public function dismiss(int $caseGoalId)
    {
        $caseGoal = DB::table('case_goals')->find($caseGoalId);
        abort_if(!$caseGoal, 404);
        
        // Delete the goal instance too since it was system-suggested and never approved
        DB::table('case_goals')->where('id', $caseGoalId)->delete();
        DB::table('goals')->where('id', $caseGoal->goal_id)->delete();

        return back()->with('success', 'Suggestion dismissed.');
    }

    public function completeTask(int $taskId)
    {
        DB::table('tasks')
            ->where('id', $taskId)
            ->update(['completed_at' => now(), 'completed_by' => auth()->id(), 'updated_at' => now()]);

        return back();
    }

    // Add to GoalController

public function publishTask(Request $request, int $taskId)
{
    $validated = $request->validate([
        'title'       => 'required|string|max:255',
        'description' => 'nullable|string',
    ]);

    DB::table('tasks')->where('id', $taskId)->update([
        'title'         => $validated['title'],
        'description'   => $validated['description'] ?? null,
        'child_visible' => true,
        'updated_at'    => now(),
    ]);

    return back()->with('success', 'Task sent to child.');
}

    public function deleteTask(int $taskId)
    {
        DB::table('task_goal')->where('task_id', $taskId)->delete();
        DB::table('tasks')->where('id', $taskId)->delete();

        return back()->with('success', 'Task removed.');
    }
}