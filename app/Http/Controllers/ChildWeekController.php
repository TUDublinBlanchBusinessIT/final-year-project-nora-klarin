<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
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

    $start = Carbon::today();
    $end = Carbon::today()->copy()->addDays(6);
    $appointments = \App\Models\Appointment::query()
    ->where(function($q) use ($userId) {
        $q->whereHas('users', function ($query) use ($userId) {
                $query->where('users.id', $userId);
            })
            ->orWhereHas('caseFile', function ($query) use ($userId) {
                $query->where('young_person_id', $userId);
            });
    })
    ->where('start_time', '>=', Carbon::today())
    ->where('start_time', '<=', Carbon::today()->copy()->addDays(29))
    ->orderBy('start_time')
    ->get();

    $moods = DB::table('mood_checkins')
        ->where('user_id', $userId)
        ->whereBetween('date', [$start, $end])
        ->pluck('mood', 'date');

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
        'start' => $start,
        'end' => $end,
        'days' => $days,
        'weeklyGoal' => $weeklyGoal,
        'goalLabel' => $goalLabel,
        'appointments' => $appointments, 
    ]);
}

}


