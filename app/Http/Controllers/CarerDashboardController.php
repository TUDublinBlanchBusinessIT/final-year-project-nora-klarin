<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Message;
use App\Models\CaseFile;

class CarerDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if (($user->role ?? null) !== 'carer') {
            abort(403);
        }

        $carer = DB::table('users')

            ->where(function ($q) use ($user) {

                if (! empty($user->username)) {

                    $q->where('username', $user->username);

                }

                $q->orWhere('email', $user->email);

            })

            ->first();


        $case = CaseFile::whereHas('carers', function ($q) use ($user) {
            $q->where('users.id', $user->id);
        })->first();

        $appointments = collect();
        $alerts = collect();
        $unreadCount = 0;
        $reminderCount = 0;

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

            if ($schema->hasTable('alerts')) {

                try {

                    $alerts = DB::table('alerts')->orderByDesc('created_at')->limit(5)->get();

                } catch (\Exception $e) {

                    $alerts = collect();

                }

            }

        } else {

            // no carer row; still show alerts if present

            if ($schema->hasTable('alerts')) {

                try {

                    $alerts = DB::table('alerts')->orderByDesc('created_at')->limit(5)->get();

                } catch (\Exception $e) {

                    $alerts = collect();

                }

            }

        }



        
        try {
            $unreadCount = Message::where('recipient_id', $user->id)
                ->whereNull('read_at')
                ->count();
        } catch (\Exception $e) {
            $unreadCount = 0;
        }

        // fallback appointments using relationship (cleaner)
        if (method_exists($user, 'appointments')) {
            $appointments = $user->appointments()
                ->where('start_time', '>=', Carbon::now())
                ->orderBy('start_time')
                ->limit(5)
                ->get();
        }

        // alerts
        if ($schema->hasTable('alerts')) {
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
