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

        // Safety: if user isn't logged in, send to login
        if (!$userId) {
            return redirect()->route('login');
        }

        // Start from TODAY, not Monday
        $start = Carbon::today();
        $end = Carbon::today()->copy()->addDays(6);

        // Mood checkins for the next 7 days starting today
        $moods = DB::table('mood_checkins')
            ->where('user_id', $userId)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->pluck('mood', 'date'); // [ 'YYYY-MM-DD' => 'happy', ... ]

        // Keep weekly goal logic based on real calendar week (Mon -> Sun)
        $weekStart = Carbon::today()->copy()->startOfWeek(Carbon::MONDAY)->toDateString();

        $weeklyGoal = DB::table('weekly_goals')
            ->where('user_id', $userId)
            ->where('week_start', $weekStart)
            ->first();

        // Fallback: show latest saved goal if none for this exact week
        if (!$weeklyGoal) {
            $weeklyGoal = DB::table('weekly_goals')
                ->where('user_id', $userId)
                ->orderByDesc('week_start')
                ->orderByDesc('id')
                ->first();
        }

        // Nice label mapping for UI
        $goalLabels = [
            'sleep' => 'Sleep on time',
            'talk'  => 'Talk to someone I trust',
            'fun'   => 'Do something fun',
            'water' => 'Drink water',
        ];

        $goalLabel = $weeklyGoal && isset($goalLabels[$weeklyGoal->goal_key])
            ? $goalLabels[$weeklyGoal->goal_key]
            : null;

        // Build 7-day list starting from TODAY
        $days = collect(range(0, 6))->map(function ($i) use ($start, $moods) {
            $date = $start->copy()->addDays($i);
            $key = $date->toDateString();

            return [
                'label' => $i === 0 ? 'Today' : $date->format('D'),
                'full'  => $date->format('l'),
                'date'  => $key,
                'display_date' => $date->format('j M'),
                'is_today' => $date->isToday(),
                'mood'  => $moods[$key] ?? null,
            ];
        });

        return view('child.week', [
            'start' => $start->toDateString(),
            'end' => $end->toDateString(),
            'days' => $days,
            'weeklyGoal' => $weeklyGoal,
            'goalLabel' => $goalLabel,
        ]);
    }
}