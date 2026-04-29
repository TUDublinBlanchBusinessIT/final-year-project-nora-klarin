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
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        $userId = $user->id;

        // Start from TODAY, not Monday
        $start = Carbon::today();
        $end = Carbon::today()->copy()->addDays(6);

        // Mood checkins for the next 7 days starting today
        $moods = DB::table('mood_checkins')
            ->where('user_id', $userId)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->pluck('mood', 'date');

        // Weekly goal logic
        $weekStart = Carbon::today()->copy()->startOfWeek(Carbon::MONDAY)->toDateString();

        $weeklyGoal = DB::table('weekly_goals')
            ->where('user_id', $userId)
            ->where('week_start', $weekStart)
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

        // Appointments for next 30 days
        $appointments = Appointment::where(function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->orWhereHas('users', function ($q) use ($userId) {
                        $q->where('users.id', $userId);
                    });
            })
            ->whereBetween('date', [
                Carbon::today()->toDateString(),
                Carbon::today()->copy()->addDays(29)->toDateString(),
            ])
            ->orderBy('date')
            ->orderBy('time')
            ->distinct()
            ->get();

        return view('child.week', [
            'start' => $start->toDateString(),
            'end' => $end->toDateString(),
            'days' => $days,
            'weeklyGoal' => $weeklyGoal,
            'goalLabel' => $goalLabel,
            'appointments' => $appointments,
        ]);
    }
}