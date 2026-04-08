<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MoodCheckinController extends Controller
{
    public function store(string $mood)
    {
        $allowed = ['happy', 'calm', 'okay', 'worried', 'sad'];
        abort_unless(in_array($mood, $allowed, true), 404);

        DB::table('mood_checkins')->updateOrInsert(
            [
                'user_id' => auth()->id(),
                'date' => now()->toDateString(),
            ],
            [
                'mood' => $mood,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        return back()->with('success', "Mood saved: {$mood}");
    }
}
