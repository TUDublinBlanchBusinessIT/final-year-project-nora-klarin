<?php



namespace App\Http\Controllers;



use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

use Carbon\Carbon;



class CarerCalendarController extends Controller

{

    private function ensureCarer(Request $request)

    {

        $user = $request->user();



        if (($user->role ?? null) !== 'carer') {

            abort(403, 'Unauthorized');

        }



        return $user;

    }



    public function index(Request $request)

    {

        $user = $this->ensureCarer($request);



        $month = (int) $request->query('month', now()->month);

        $year = (int) $request->query('year', now()->year);



        $monthStart = Carbon::create($year, $month, 1)->startOfMonth();

        $monthEnd = $monthStart->copy()->endOfMonth();



        $caseIds = DB::table('case_user')

            ->where('user_id', $user->id)

            ->pluck('case_id');



        $appointments = DB::table('appointments')

            ->whereIn('case_file_id', $caseIds)

            ->whereBetween('start_time', [$monthStart, $monthEnd])

            ->orderBy('start_time')

            ->get();



        $eventsByDate = $appointments->groupBy(function ($appt) {

            return Carbon::parse($appt->start_time)->format('Y-m-d');

        });



        return view('carer.calendar', [

            'user' => $user,

            'appointments' => $appointments,

            'eventsByDate' => $eventsByDate,

            'monthStart' => $monthStart,

            'monthEnd' => $monthEnd,

            'month' => $month,

            'year' => $year,

            'caseIds' => $caseIds,

        ]);

    }



    public function store(Request $request)

    {

        $user = $this->ensureCarer($request);



        $caseIds = DB::table('case_user')

            ->where('user_id', $user->id)

            ->pluck('case_id')

            ->toArray();



        $data = $request->validate([

            'case_file_id' => ['required', 'integer'],

            'title' => ['required', 'string', 'max:255'],

            'location' => ['nullable', 'string', 'max:255'],

            'notes' => ['nullable', 'string', 'max:1000'],

            'start_time' => ['required', 'date'],

            'end_time' => ['required', 'date', 'after_or_equal:start_time'],

        ]);



        if (!in_array((int) $data['case_file_id'], $caseIds, true)) {

            abort(403);

        }



        DB::table('appointments')->insert([

            'case_file_id' => $data['case_file_id'],

            'created_by' => $user->id,

            'young_person_id' => 4,

            'title' => $data['title'],

            'location' => $data['location'],

            'notes' => $data['notes'],

            'start_time' => $data['start_time'],

            'end_time' => $data['end_time'],

            'created_at' => now(),

            'updated_at' => now(),

        ]);



        return redirect()

            ->route('carer.calendar', [

                'month' => Carbon::parse($data['start_time'])->month,

                'year' => Carbon::parse($data['start_time'])->year,

            ])

            ->with('status', 'Event added to calendar.');

    }

}

