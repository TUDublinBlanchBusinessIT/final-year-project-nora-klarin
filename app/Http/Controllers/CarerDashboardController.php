<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use App\Models\CaseFile;

class CarerDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if (($user->role ?? null) !== 'carer') {
            abort(403);
        }

        $case = CaseFile::whereHas('carers', function ($q) use ($user) {
            $q->where('users.id', $user->id);
        })->first();

        $appointments = collect();
        $alerts = collect();
        $unreadCount = 0;
        $reminderCount = 0;

        if (method_exists($user, 'appointments')) {
            $appointments = $user->appointments()
                ->where('start_time', '>=', Carbon::now())
                ->orderBy('start_time')
                ->limit(5)
                ->get();
        }

        if (Schema::hasTable('alerts')) {
            $alerts = DB::table('alerts')
                ->orderByDesc('created_at')
                ->limit(5)
                ->get();

            $reminderCount = $alerts->count();
        }

        return view('carer.dashboard', [
            'user' => $user,
            'case' => $case,
            'appointments' => $appointments,
            'alerts' => $alerts,
            'unreadCount' => $unreadCount,
            'reminderCount' => $reminderCount,
        ]);
    }
}