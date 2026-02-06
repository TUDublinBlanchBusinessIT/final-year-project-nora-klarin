<?php

namespace App\Http\Controllers;

use App\Models\WeeklyGoal;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ChildGoalsController extends Controller
{
    public function index()
    {
        // The goals you show on the page
        $goals = [
            'sleep' => 'Sleep on time',
            'talk'  => 'Talk to someone I trust',
            'fun'   => 'Do something fun',
            'water' => 'Drink water',
        ];

        // Monday of the current week
        $weekStart = Carbon::now()->startOfWeek(Carbon::MONDAY)->toDateString();

        // Load this user's goal for the week (if any)
        $current = WeeklyGoal::where('user_id', auth()->id())
            ->where('week_start', $weekStart)
            ->first();

        return view('child.goals', [
            'goals' => $goals,
            'currentGoalKey' => $current?->goal_key,
        ]);
    }

    public function store(Request $request)
    {
        $allowed = ['sleep', 'talk', 'fun', 'water'];

        $data = $request->validate([
            'goal_key' => ['required', 'in:' . implode(',', $allowed)],
        ]);

        $weekStart = Carbon::now()->startOfWeek(Carbon::MONDAY)->toDateString();

        WeeklyGoal::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'week_start' => $weekStart,
            ],
            [
                'goal_key' => $data['goal_key'],
            ]
        );

        return back()->with('success', 'Weekly goal saved ✅');
    }
}
