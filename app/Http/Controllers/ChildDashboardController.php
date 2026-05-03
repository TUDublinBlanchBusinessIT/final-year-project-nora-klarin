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
        $child = Auth::user();
        $userId = $child->id;

        // Linked carer (if assigned)
        $carer = null;
        if (!empty($child->carer_id)) {
            $carer = User::find($child->carer_id);
        }

        // Get latest diary entries (max 3)
        $recentEntries = DB::table('diary_entries')
            ->where('user_id', $userId)
            ->orderByDesc('created_at')
            ->limit(3)
            ->get();

        // Check if diary entry exists today
        $hasEntryToday = DB::table('diary_entries')
            ->where('user_id', $userId)
            ->whereDate('created_at', Carbon::today())
            ->exists();

        // Reminder count
        $reminderCount = $hasEntryToday ? 0 : 1;

        // Unread messages count
        $unreadMessageCount = DB::table('messages')
            ->join('threads', 'messages.thread_id', '=', 'threads.id')
            ->where('threads.child_id', $userId)
            ->where('messages.sender_id', '!=', $userId)
            ->whereNull('messages.read_at')
            ->count();

        // Get case and goals
        $case = CaseFile::where('young_person_id', $userId)->first();
        $goals = $case
            ? $case->goals()->with('goal.sourceDomain')->get()
            : collect();
        // Latest mood
        $latestMood = DB::table('mood_checkins')
            ->where('user_id', $userId)
            ->orderByDesc('date')
            ->value('mood');

        return view('child.dashboard', [
            'recentEntries' => $recentEntries,
            'reminderCount' => $reminderCount,
            'unreadMessageCount' => $unreadMessageCount,
            'carer' => $carer,
            'goals' => $goals,
            'latestMood' => $latestMood,
        ]);
    }
}