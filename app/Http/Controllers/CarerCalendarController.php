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



        // keep your existing role check

        if (($user->role ?? null) !== 'carer') {

            abort(403);

        }



        $appointments = collect();

        $now = Carbon::now();



        // Use appointment_user pivot (Laravel convention: appointment_id, user_id)

        $pivot = 'appointment_user';

        $appointmentTable = 'appointment';



        // ensure the pivot table exists and has the expected columns

        try {

            $schema = DB::getSchemaBuilder();



            if ($schema->hasTable($pivot)

                && ($schema->hasColumn($pivot, 'appointment_id') || $schema->hasColumn($pivot, 'appointmentid'))

                && ($schema->hasColumn($pivot, 'user_id') || $schema->hasColumn($pivot, 'carerid'))

                && $schema->hasTable($appointmentTable)

            ) {

                // decide which column names your DB uses

                $appointmentCol = $schema->hasColumn($pivot, 'appointment_id') ? 'appointment_id' : 'appointmentid';

                $userCol = $schema->hasColumn($pivot, 'user_id') ? 'user_id' : 'carerid';



                $appointments = DB::table($pivot)

                    ->join($appointmentTable, "{$pivot}.{$appointmentCol}", '=', "{$appointmentTable}.id")

                    ->where("{$pivot}.{$userCol}", $user->id)

                    ->where("{$appointmentTable}.starttime", '>=', $now)

                    ->orderBy("{$appointmentTable}.starttime")

                    ->select("{$appointmentTable}.starttime", "{$appointmentTable}.endtime", "{$appointmentTable}.notes")

                    ->limit(5)

                    ->get();

            }

        } catch (\Exception $e) {

            // if anything goes wrong with schema checks, return an empty collection

            // (you can log $e if you'd like)

            $appointments = collect();

        }



        return view('carer.calendar', compact('appointments', 'user'));

    }

}