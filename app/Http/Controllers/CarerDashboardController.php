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



        // Try to find the "carer" row in users by username OR email (safer)

        $carer = DB::table('users')

            ->where(function ($q) use ($user) {

                if (! empty($user->username)) {

                    $q->where('username', $user->username);

                }

                $q->orWhere('email', $user->email);

            })

            ->first();



        $appointments = collect();

        $alerts = collect();



        $schema = DB::getSchemaBuilder();



        if ($carer) {

            // Use appointment_user pivot and appointment table

            $pivot = 'appointment_user';

            $appointmentTable = $schema->hasTable('appointment') ? 'appointment' : ($schema->hasTable('appointments') ? 'appointments' : null);



            if ($schema->hasTable($pivot) && $appointmentTable) {

                // Detect column name variants defensively

                $appointmentCol = $schema->hasColumn($pivot, 'appointment_id') ? 'appointment_id' : ( $schema->hasColumn($pivot, 'appointmentid') ? 'appointmentid' : null );

                $userCol = $schema->hasColumn($pivot, 'user_id') ? 'user_id' : ( $schema->hasColumn($pivot, 'carerid') ? 'carerid' : ( $schema->hasColumn($pivot, 'carer_id') ? 'carer_id' : null ) );



                if ($appointmentCol && $userCol) {

                    try {

                        $appointments = DB::table($pivot)

                            ->join($appointmentTable, "{$pivot}.{$appointmentCol}", '=', "{$appointmentTable}.id")

                            ->where("{$pivot}.{$userCol}", $carer->id)

                            ->where("{$appointmentTable}.starttime", '>=', Carbon::now())

                            ->orderBy("{$appointmentTable}.starttime")

                            ->limit(5)

                            ->select("{$appointmentTable}.starttime", "{$appointmentTable}.endtime", "{$appointmentTable}.notes")

                            ->get();

                    } catch (\Exception $e) {

                        // keep $appointments empty if something goes wrong

                        $appointments = collect();

                    }

                }

            } else {

                // If pivot/table not present, leave $appointments empty (no crash)

                $appointments = collect();

            }



            // Alerts (defensive)

            if ($schema->hasTable('alert')) {

                try {

                    $alerts = DB::table('alert')->orderByDesc('createdat')->limit(5)->get();

                } catch (\Exception $e) {

                    $alerts = collect();

                }

            }

        } else {

            // no carer row; still show alerts if present

            if ($schema->hasTable('alert')) {

                try {

                    $alerts = DB::table('alert')->orderByDesc('createdat')->limit(5)->get();

                } catch (\Exception $e) {

                    $alerts = collect();

                }

            }

        }



        // unread messages (defensive)

        try {

            $unreadCount = Message::where('recipient_id', $user->id)->whereNull('read_at')->count();

        } catch (\Exception $e) {

            $unreadCount = 0;

        }



        return view('carer.dashboard', compact('user', 'appointments', 'carer', 'alerts', 'unreadCount'));

    }

}


