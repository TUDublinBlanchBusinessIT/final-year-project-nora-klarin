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

        
            // Next 5 upcoming appointments for this foster carer
$appointments = $user->appointments()  // via the pivot table
    ->where('start_time', '>=', Carbon::now())
    ->orderBy('start_time')
    ->limit(5)
    ->get();



            // Latest 5 alerts for the carer's cases (if you have case links)
            // If you don't have case relationships yet, we just show latest alerts overall.
            $alerts = DB::table('alerts')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        
            // still show latest alerts even if no carer record exists yet


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
