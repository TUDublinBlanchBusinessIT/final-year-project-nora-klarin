<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ChildWeekController extends Controller
{
    public function index()
{
    $user = auth()->user();

    if (!$user) {
        return redirect()->route('login');
    }

    abort_if($user->role !== 'young_person', 403);

    $userId = $user->id;

    $start = Carbon::now()->startOfWeek(Carbon::MONDAY)->toDateString();
    $end   = Carbon::now()->endOfWeek(Carbon::SUNDAY)->toDateString();

    $appointments = \App\Models\Appointment::whereHas('caseFile', function ($query) use ($userId) {
        $query->where('young_person_id', $userId);
    })
    ->whereBetween('start_time', [
        Carbon::parse($start)->startOfDay(),
        Carbon::parse($end)->endOfDay()
    ])
    ->orderBy('start_time')
    ->get();

    // Mood checkins for this week
    $moods = DB::table('mood_checkins')
        ->where('user_id', $userId)
        ->whereBetween('date', [$start, $end])
        ->pluck('mood', 'date');

    // Weekly goal for THIS week
    $weeklyGoal = DB::table('weekly_goals')
        ->where('user_id', $userId)
        ->where('week_start', $start)
        ->first();

    if (!$weeklyGoal) {
        $weeklyGoal = DB::table('weekly_goals')
            ->where('user_id', $userId)
            ->orderByDesc('week_start')
            ->orderByDesc('id')
            ->first();
    }

    $goalLabels = [
        'sleep' => 'Sleep on time',
        'talk'  => 'Talk to someone I trust',
        'fun'   => 'Do something fun',
        'water' => 'Drink water',
    ];

    $goalLabel = $weeklyGoal && isset($goalLabels[$weeklyGoal->goal_key])
        ? $goalLabels[$weeklyGoal->goal_key]
        : null;

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
        'appointments' => $appointments, 
    ]);
}

}
