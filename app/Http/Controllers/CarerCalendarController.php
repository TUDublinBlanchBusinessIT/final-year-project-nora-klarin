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
            abort(403);
        }

        // Match logged-in user to fostercarer record by email
        $carer = DB::table('fostercarer')
            ->where('email', $user->email)
            ->first();

        $appointments = collect();

        if ($carer) {
            $appointments = DB::table('carerappointment')
                ->join('appointment', 'carerappointment.appointmentid', '=', 'appointment.id')
                ->where('carerappointment.carerid', $carer->id)
                ->where('appointment.starttime', '>=', Carbon::now())
                ->orderBy('appointment.starttime')
                ->select('appointment.starttime', 'appointment.endtime', 'appointment.notes')
                ->get();
        }

        return view('carer.calendar', compact('appointments', 'carer'));
    }
}
