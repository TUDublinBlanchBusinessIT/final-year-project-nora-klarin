<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ChildDashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

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

        // Reminder count (future-ready)
        $reminderCount = $hasEntryToday ? 0 : 1;

        return view('child.dashboard', [
            'recentEntries' => $recentEntries,
            'reminderCount' => $reminderCount,
        ]);
    }
}
