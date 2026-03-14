<?php



namespace App\Http\Controllers;



use Illuminate\Http\Request;

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