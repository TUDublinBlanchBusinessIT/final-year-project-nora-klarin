<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Message;

class CarerDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if (($user->role ?? null) !== 'carer') {
            abort(403);
        }

        // Find the foster carer record that matches this logged-in user's email
        $carer = DB::table('users')
            ->where('username', $user->username)
            ->first();

        // Defaults so the page still loads nicely
        $appointments = collect();
        $alerts = collect();

        if ($carer) {
            // Next 5 upcoming appointments for this foster carer
            $appointments = DB::table('carerappointment')
                ->join('appointment', 'carerappointment.appointmentid', '=', 'appointment.id')
                ->where('carerappointment.carerid', $carer->id) // <-- fostercarer.id
                ->where('appointment.starttime', '>=', Carbon::now())
                ->orderBy('appointment.starttime')
                ->limit(5)
                ->select('appointment.starttime', 'appointment.endtime', 'appointment.notes')
                ->get();

            // Latest 5 alerts for the carer's cases (if you have case links)
            // If you don't have case relationships yet, we just show latest alerts overall.
            $alerts = DB::table('alert')
                ->orderByDesc('createdat')
                ->limit(5)
                ->get();
        } else {
            // still show latest alerts even if no carer record exists yet
            $alerts = DB::table('alert')
                ->orderByDesc('createdat')
                ->limit(5)
                ->get();
        }

        // Unread messages count for this logged-in user
        $unreadCount = Message::where('recipient_id', $user->id)
            ->whereNull('read_at')
            ->count();

        return view('carer.dashboard', compact(
            'user',
            'appointments',
            'carer',
            'alerts',
            'unreadCount'
        ));
    }
}
