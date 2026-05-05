<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\CaseFile;

class ChildDashboardController extends Controller
{
    public function index()
    {
        $child  = Auth::user();
        $userId = $child->id;

        // Linked carer (if assigned)
        $carer = null;
        if (!empty($child->carer_id)) {
            $carer = User::find($child->carer_id);
        }

        // Recent diary entries (max 3)
        $recentEntries = DB::table('diary_entries')
            ->where('user_id', $userId)
            ->orderByDesc('created_at')
            ->limit(3)
            ->get();

        // Reminder if no diary entry today
        $hasEntryToday = DB::table('diary_entries')
            ->where('user_id', $userId)
            ->whereDate('created_at', Carbon::today())
            ->exists();

        $reminderCount = $hasEntryToday ? 0 : 1;

        // Unread messages
        $unreadMessageCount = DB::table('messages')
            ->join('threads', 'messages.thread_id', '=', 'threads.id')
            ->where('threads.child_id', $userId)
            ->where('messages.sender_id', '!=', $userId)
            ->whereNull('messages.read_at')
            ->count();

        // Latest mood check-in
        $latestMood = DB::table('mood_checkins')
            ->where('user_id', $userId)
            ->orderByDesc('date')
            ->value('mood');

        // Today's mood (for highlighting the mood strip)
        $todayMood = DB::table('mood_checkins')
            ->where('user_id', $userId)
            ->whereDate('date', Carbon::today())
            ->value('mood');

        // ── Goals (same query as ChildGoalsController) ────────────────────
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
                'goals.title',
                'goals.description',
                'domains.name as domain_name',
            )
            ->get();

        $caseGoalIds = $activeGoals->pluck('case_goal_id');

        $tasksByGoal = DB::table('tasks')
            ->whereIn('case_goal_id', $caseGoalIds)
            ->where('child_visible', true)
            ->select('id', 'case_goal_id', 'title', 'description', 'completed_at')
            ->get()
            ->groupBy('case_goal_id');

        return view('child.dashboard', [
            'recentEntries'      => $recentEntries,
            'reminderCount'      => $reminderCount,
            'unreadMessageCount' => $unreadMessageCount,
            'carer'              => $carer,
            'latestMood'         => $latestMood,
            'todayMood'          => $todayMood,
            'activeGoals'        => $activeGoals,
            'tasksByGoal'        => $tasksByGoal,
        ]);
    }
}