<?php

namespace App\Console\Commands;

use App\Models\User;

use App\Notifications\CareHubNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class NotifyOverdueWellbeingChecks extends Command
{
    protected $signature   = 'wellbeing:notify-overdue {--days=30}';
    protected $description = 'Notify social workers when a young person has not completed a wellbeing check in N days.';

    public function handle(): void
    {
        $days = (int) $this->option('days');

        // Find young people with open cases whose last completed check
        // is older than $days ago, or who have never completed one.
        $overdue = DB::table('case_files')
            ->join('users as yp', 'case_files.young_person_id', '=', 'yp.id')
            ->leftJoin('wellbeing_checks as wc', function ($join) {
                $join->on('wc.young_person_id', '=', 'case_files.young_person_id')
                     ->whereNotNull('wc.completed_at');
            })
            ->where('case_files.status', 'open')
            ->select(
                'case_files.id as case_file_id',
                'case_files.young_person_id',
                'yp.name as child_name',
                DB::raw('MAX(wc.completed_at) as last_check')
            )
            ->groupBy('case_files.id', 'case_files.young_person_id', 'yp.name')
            ->havingRaw('last_check IS NULL OR last_check < NOW() - INTERVAL ? DAY', [$days])
            ->get();

        foreach ($overdue as $row) {
            $daysSince = $row->last_check
                ? (int) now()->diffInDays($row->last_check)
                : $days;

            // Find the assigned social workers for this case
            $workerIds = DB::table('case_user')
                ->where('case_file_id', $row->case_file_id)
                ->where('role', 'social_worker')
                ->pluck('user_id');

            $workers = User::whereIn('id', $workerIds)->get();

            foreach ($workers as $worker) {
                // Only notify once per day per child — check for existing unread notification
                $alreadyNotified = $worker->unreadNotifications()
                    ->where('data->type', 'overdue_wellbeing')
                    ->where('data->case_file_id', $row->case_file_id)
                    ->whereDate('created_at', today())
                    ->exists();

                if (! $alreadyNotified) {
                    $worker->notify(new CareHubNotification(
                        type: 'overdue_wellbeing',
                        summary: 'No wellbeing check for ' . $row->child_name . ' in ' . $daysSince . ' days.',
                        data: ['case_file_id' => $row->case_file_id, 'days_since' => $daysSince],
                    ));
                }
            }

            $this->info("Notified workers for {$row->child_name} ({$daysSince} days overdue)");
        }
    }
}
