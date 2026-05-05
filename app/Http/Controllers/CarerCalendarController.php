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
        $year  = (int) $request->query('year',  now()->year);

        $monthStart = Carbon::create($year, $month, 1)->startOfMonth();
        $monthEnd   = $monthStart->copy()->endOfMonth();

        // Widen the window to cover the full calendar grid (prev/next month overflow days)
        $calendarStart = $monthStart->copy()->startOfWeek(Carbon::MONDAY);
        $calendarEnd   = $monthEnd->copy()->endOfWeek(Carbon::SUNDAY);

        $caseIds = DB::table('case_user')
            ->where('user_id', $user->id)
            ->pluck('case_file_id');

        // Fetch ALL appointments for the carer's cases in the calendar window
        // (includes both social worker-created and carer-created ones)
        $appointments = DB::table('appointments')
            ->whereIn('case_file_id', $caseIds)
            ->whereBetween('start_time', [$calendarStart, $calendarEnd])
            ->orderBy('start_time')
            ->get();

        $eventsByDate = $appointments->groupBy(function ($appt) {
            return Carbon::parse($appt->start_time)->format('Y-m-d');
        });

        // Upcoming = from today onwards, not limited to this month
        $upcoming = DB::table('appointments')
            ->whereIn('case_file_id', $caseIds)
            ->where('start_time', '>=', now())
            ->orderBy('start_time')
            ->limit(10)
            ->get();

        return view('carer.calendar', [
            'user'         => $user,
            'appointments' => $upcoming,  
            'eventsByDate' => $eventsByDate,
            'monthStart'   => $monthStart,
            'monthEnd'     => $monthEnd,
            'month'        => $month,
            'year'         => $year,
            'caseIds'      => $caseIds,
        ]);
    }

    public function store(Request $request)
    {
        $user = $this->ensureCarer($request);

        $caseIds = DB::table('case_user')
            ->where('user_id', $user->id)
            ->pluck('case_file_id')
            ->toArray();

        $data = $request->validate([
            'case_file_id' => ['nullable', 'integer'],
            'title'        => ['required', 'string', 'max:255'],
            'location'     => ['nullable', 'string', 'max:255'],
            'notes'        => ['nullable', 'string', 'max:1000'],
            'start_time'   => ['required', 'date'],
            'end_time'     => ['required', 'date', 'after_or_equal:start_time'],
        ]);

        $caseFileId = null;
        if (!empty($data['case_file_id'])) {
            if (!in_array((int) $data['case_file_id'], $caseIds, true)) {
                abort(403, 'You do not have access to this case.');
            }
            $caseFileId = (int) $data['case_file_id'];
        } else {
            $caseFileId = $caseIds[0] ?? null;
        }

        if (!$caseFileId) {
            return back()->withErrors(['case_file_id' => 'No case file available.']);
        }

        $youngPersonId = DB::table('case_files')
            ->where('id', $caseFileId)
            ->value('young_person_id');

        DB::table('appointments')->insert([
            'case_file_id'    => $caseFileId,
            'created_by'      => $user->id,
            'title'           => $data['title'],
            'location'        => $data['location'] ?? null,
            'start_time'      => $data['start_time'],
            'end_time'        => $data['end_time'],
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        return redirect()
            ->route('carer.calendar', [
                'month' => Carbon::parse($data['start_time'])->month,
                'year'  => Carbon::parse($data['start_time'])->year,
            ])
            ->with('status', 'Event added to calendar.');
    }
}