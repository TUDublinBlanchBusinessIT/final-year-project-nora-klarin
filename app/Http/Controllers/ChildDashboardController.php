<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChildDashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // Get latest diary entries for this child (max 3)
        $recentEntries = DB::table('diary_entries')
            ->where('user_id', $userId)
            ->orderByDesc('created_at')
            ->limit(3)
            ->get();

        return view('child.dashboard', [
            'recentEntries' => $recentEntries,
        ]);
    }
}
