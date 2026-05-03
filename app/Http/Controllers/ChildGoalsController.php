<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChildGoalsController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        $activeGoals = DB::table('case_goals')
            ->join('goals', 'case_goals.goal_id', '=', 'goals.id')
            ->join('case_files', 'case_goals.case_file_id', '=', 'case_files.id')
            ->leftJoin('domains', 'goals.source_domain_id', '=', 'domains.id')
            ->where('case_files.young_person_id', $userId)
            ->where('case_goals.child_visible', 1)
            ->where('case_goals.status', 'in_progress')
            ->select(
                'case_goals.id as case_goal_id',
                'case_goals.due_date',
                'case_goals.child_accepted_at',
                'goals.id as goal_id',
                'goals.title',
                'goals.description',
                'domains.name as domain_name',
            )
            ->get();

        $pendingGoals = DB::table('case_goals')
            ->join('goals', 'case_goals.goal_id', '=', 'goals.id')
            ->join('case_files', 'case_goals.case_file_id', '=', 'case_files.id')
            ->leftJoin('domains', 'goals.source_domain_id', '=', 'domains.id')
            ->where('case_files.young_person_id', $userId)
            ->where('case_goals.child_visible', 1)
            ->whereNull('case_goals.child_accepted_at')
            ->where('case_goals.status', 'in_progress')
            ->select(
                'case_goals.id as case_goal_id',
                'goals.title',
                'goals.description',
                'domains.name as domain_name',
            )
            ->get();

        $completedGoals = DB::table('case_goals')
            ->join('goals', 'case_goals.goal_id', '=', 'goals.id')
            ->join('case_files', 'case_goals.case_file_id', '=', 'case_files.id')
            ->leftJoin('domains', 'goals.source_domain_id', '=', 'domains.id')
            ->where('case_files.young_person_id', $userId)
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
            ->select('id', 'case_goal_id', 'title', 'description', 'completed_at')
            ->get()
            ->groupBy('case_goal_id');

        return view('child.goals', compact(
            'activeGoals',
            'pendingGoals',
            'completedGoals',
            'tasksByCaseGoal',
        ));
    }

    public function accept(int $caseGoalId)
    {
        $userId = auth()->id();

        $valid = DB::table('case_goals')
            ->join('case_files', 'case_goals.case_file_id', '=', 'case_files.id')
            ->where('case_goals.id', $caseGoalId)
            ->where('case_files.young_person_id', $userId)
            ->where('case_goals.child_visible', 1)
            ->exists();

        abort_if(!$valid, 403);

        DB::table('case_goals')
            ->where('id', $caseGoalId)
            ->update([
                'child_accepted_at' => now(),
                'updated_at'        => now(),
            ]);

        return back()->with('success', 'Goal accepted!');
    }

    public function completeTask(int $taskId)
    {
        $userId = auth()->id();

        // Verify this task belongs to a goal assigned to this child
        $valid = DB::table('tasks')
            ->join('case_goals', 'tasks.case_goal_id', '=', 'case_goals.id')
            ->join('case_files', 'case_goals.case_file_id', '=', 'case_files.id')
            ->where('tasks.id', $taskId)
            ->where('case_files.young_person_id', $userId)
            ->exists();

        abort_if(!$valid, 403);

        DB::table('tasks')
            ->where('id', $taskId)
            ->update([
                'completed_at' => now(),
                'completed_by' => $userId,
                'updated_at'   => now(),
            ]);

        return back()->with('success', 'Task done!');
    }

    public function uncompleteTask(int $taskId)
    {
        $userId = auth()->id();

        $valid = DB::table('tasks')
            ->join('case_goals', 'tasks.case_goal_id', '=', 'case_goals.id')
            ->join('case_files', 'case_goals.case_file_id', '=', 'case_files.id')
            ->where('tasks.id', $taskId)
            ->where('case_files.young_person_id', $userId)
            ->exists();

        abort_if(!$valid, 403);

        DB::table('tasks')
            ->where('id', $taskId)
            ->update([
                'completed_at' => null,
                'completed_by' => null,
                'updated_at'   => now(),
            ]);

        return back();
    }
}