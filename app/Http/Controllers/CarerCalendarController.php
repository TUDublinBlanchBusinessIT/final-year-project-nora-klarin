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



        $caseIds = DB::table('case_user')

            ->where('user_id', $user->id)

            ->pluck('case_id');



        $appointments = DB::table('appointments')

            ->whereIn('case_file_id', $caseIds)

            ->where('start_time', '>=', Carbon::now())

            ->orderBy('start_time')

            ->get();



        return view('carer.calendar', compact('appointments', 'user'));

    }

}

