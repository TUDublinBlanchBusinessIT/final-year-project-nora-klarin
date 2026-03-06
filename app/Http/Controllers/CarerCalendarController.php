<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CarerCalendarController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if (($user->role ?? null) !== 'carer') {
            abort(403, 'Unauthorized');
        }

        $appointments = collect();

        if ($carer) {
            $appointments = DB::table('carer_appointment')
                ->join('appointment', 'carer_appointment.appointmentid', '=', 'appointment.id')
                ->where('carer_appointment.carerid', $carer->id)
                ->where('appointment.starttime', '>=', Carbon::now())
                ->orderBy('appointment.starttime')
                ->select('appointment.starttime', 'appointment.endtime', 'appointment.notes')
                ->get();
        }

        return view('carer.calendar', compact('appointments', 'carer'));
    }
}
