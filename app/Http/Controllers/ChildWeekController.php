<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ChildWeekController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // Safety: if user isn't logged in, send to login (shouldn't happen due to middleware)
        if (!$userId) {
            return redirect()->route('login');
        }

        // Week range (Mon -> Sun)
        $start = Carbon::now()->startOfWeek(Carbon::MONDAY)->toDateString();
        $end   = Carbon::now()->endOfWeek(Carbon::SUNDAY)->toDateString();

        // Mood checkins for this week
        $moods = DB::table('mood_checkins')
            ->where('user_id', $userId)
            ->whereBetween('date', [$start, $end])
            ->pluck('mood', 'date'); // [ 'YYYY-MM-DD' => 'happy', ... ]

        // Weekly goal for THIS week (exact match)
        $weeklyGoal = DB::table('weekly_goals')
            ->where('user_id', $userId)
            ->where('week_start', $start)
            ->first();

        // Fallback: if none saved for this exact week, show latest goal saved
        if (!$weeklyGoal) {
            $weeklyGoal = DB::table('weekly_goals')
                ->where('user_id', $userId)
                ->orderByDesc('week_start')
                ->orderByDesc('id')
                ->first();
        }

        // Optional: nice label mapping for UI
        $goalLabels = [
            'sleep' => 'Sleep on time',
            'talk'  => 'Talk to someone I trust',
            'fun'   => 'Do something fun',
            'water' => 'Drink water',
        ];

        $goalLabel = $weeklyGoal && isset($goalLabels[$weeklyGoal->goal_key])
            ? $goalLabels[$weeklyGoal->goal_key]
            : null;

        // Build 7 days list for UI
        $days = collect(range(0, 6))->map(function ($i) use ($start, $moods) {
            $date = Carbon::parse($start)->addDays($i);
            $key = $date->toDateString();

            return [
                'label' => $date->format('D'),
                'full'  => $date->format('l'),
                'date'  => $key,
                'mood'  => $moods[$key] ?? null,
            ];
        });

        return view('child.week', [
            'start' => $start,
            'end' => $end,
            'days' => $days,
            'weeklyGoal' => $weeklyGoal,
            'goalLabel' => $goalLabel,
        ]);
    }
}
