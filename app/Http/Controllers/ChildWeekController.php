<?php



namespace App\Http\Controllers;



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



        $start = Carbon::now()->startOfWeek(Carbon::MONDAY);

        $end = Carbon::now()->endOfWeek(Carbon::SUNDAY);



        $appointments = \App\Models\Appointment::whereHas('caseFile', function ($query) use ($userId) {

            $query->where('young_person_id', $userId)

                  ->orWhere('youngpersonid', $userId);

        })

        ->whereBetween('start_time', [

            $start->copy()->startOfDay(),

            $end->copy()->endOfDay()

        ])

        ->orderBy('start_time')

        ->get();



        $moods = DB::table('mood_checkins')

            ->where('user_id', $userId)

            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])

            ->pluck('mood', 'date');



        $weeklyGoal = DB::table('weekly_goals')

            ->where('user_id', $userId)

            ->where('week_start', $start->toDateString())

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

                'label' => $date->isToday() ? 'Today' : $date->format('D'),

                'full' => $date->format('l'),

                'date' => $key,

                'display_date' => $date->format('j M'),

                'is_today' => $date->isToday(),

                'mood' => $moods[$key] ?? null,

            ];

        });



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

