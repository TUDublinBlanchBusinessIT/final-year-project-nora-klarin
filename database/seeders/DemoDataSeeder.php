<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $now   = Carbon::now();
        $swId  = 7;
        $carerId = 1;

        // ── Fetch existing cases assigned to SW 7 ─────────────────────────────
        $cases = DB::table('case_files')
            ->join('case_user', 'case_files.id', '=', 'case_user.case_file_id')
            ->where('case_user.user_id', $swId)
            ->where('case_user.role', 'social_worker')
            ->where('case_files.status', 'open')
            ->select('case_files.id', 'case_files.young_person_id')
            ->get();

        if ($cases->isEmpty()) {
            $this->command->warn('No open cases found for SW id ' . $swId . '. Run DemoSeeder first.');
            return;
        }

        $youngPersonIds = $cases->pluck('young_person_id')->filter()->unique()->values();

        // ── Domains ───────────────────────────────────────────────────────────
        $domains = DB::table('domains')->pluck('id', 'name');

        // ── 1. MESSAGES ───────────────────────────────────────────────────────
        $this->command->info('Seeding messages...');

        $messagePairs = [
            [$swId, $carerId],
        ];

        // Add SW <-> first 3 young people
        foreach ($youngPersonIds->take(3) as $ypId) {
            $messagePairs[] = [$swId, $ypId];
        }

        $messageTemplates = [
            ['sender' => 0, 'body' => 'Hi, just checking in on the case. How are things going?'],
            ['sender' => 1, 'body' => 'Things are going well overall. There was a small incident last week but it has been resolved.'],
            ['sender' => 0, 'body' => 'Thanks for the update. Can we schedule a review meeting for next week?'],
            ['sender' => 1, 'body' => 'Of course. I am free on Tuesday or Thursday afternoon.'],
            ['sender' => 0, 'body' => 'Thursday works great. I will send a calendar invite. Also, I noticed the latest wellbeing score has improved.'],
            ['sender' => 1, 'body' => 'Yes, we have been working on a new routine and it seems to be helping. The goal tasks have made a real difference.'],
            ['sender' => 0, 'body' => 'Excellent progress. Let us keep monitoring and I will review the placement notes before our meeting.'],
        ];

        foreach ($messagePairs as [$userA, $userB]) {
            // Create thread
$childId = in_array($userB, $youngPersonIds->toArray())
    ? $userB
    : $youngPersonIds->first();

$thread = DB::table('threads')
    ->where('child_id', $childId)
    ->where('carer_id', $carerId)
    ->first();

if (!$thread) {
    $threadId = DB::table('threads')->insertGetId([
        'child_id'   => $childId,
        'carer_id'   => $carerId,
        'created_at' => $now->copy()->subDays(rand(5, 20)),
        'updated_at' => $now,
    ]);
} else {
    $threadId = $thread->id;
}

            foreach ($messageTemplates as $idx => $msg) {
                $sender    = $msg['sender'] === 0 ? $userA : $userB;
                $recipient = $msg['sender'] === 0 ? $userB : $userA;
                $sentAt    = $now->copy()->subDays(6)->addHours($idx * 4 + rand(0, 2));

                DB::table('messages')->insert([
                    'thread_id'    => $threadId,
                    'sender_id'    => $sender,
                    'recipient_id' => $recipient,
                    'body'         => $msg['body'],
                    'read_at'      => $idx < 5 ? $sentAt->copy()->addMinutes(rand(5, 60)) : null,
                    'created_at'   => $sentAt,
                    'updated_at'   => $sentAt,
                ]);
            }
        }

        // ── 2. GOALS & TASKS ─────────────────────────────────────────────────
        $this->command->info('Seeding goals and tasks...');

        $goalTemplates = [
            [
                'title'       => 'Build a daily routine',
                'description' => 'Establish a consistent morning and evening routine to support emotional stability.',
                'domain'      => 'Emotional',
                'tasks'       => [
                    ['title' => 'Create a morning checklist', 'visible' => true,  'done' => true],
                    ['title' => 'Set a regular bedtime for 5 days', 'visible' => true,  'done' => true],
                    ['title' => 'Track mood each morning for a week', 'visible' => true,  'done' => false],
                    ['title' => 'Review routine with carer', 'visible' => false, 'done' => false],
                ],
            ],
            [
                'title'       => 'Improve school attendance',
                'description' => 'Work toward consistent attendance and engagement with school.',
                'domain'      => 'Education',
                'tasks'       => [
                    ['title' => 'Attend school 5 days in a row', 'visible' => true,  'done' => true],
                    ['title' => 'Meet with form teacher', 'visible' => true,  'done' => false],
                    ['title' => 'Complete missing homework assignments', 'visible' => true,  'done' => false],
                    ['title' => 'Contact school attendance officer', 'visible' => false, 'done' => false],
                ],
            ],
            [
                'title'       => 'Strengthen social connections',
                'description' => 'Build meaningful relationships with peers and trusted adults.',
                'domain'      => 'Social',
                'tasks'       => [
                    ['title' => 'Join one after-school activity', 'visible' => true,  'done' => true],
                    ['title' => 'Meet a friend outside school this week', 'visible' => true,  'done' => false],
                    ['title' => 'Write about a positive social experience', 'visible' => true,  'done' => false],
                ],
            ],
            [
                'title'       => 'Develop physical health habits',
                'description' => 'Introduce regular physical activity and healthier eating patterns.',
                'domain'      => 'Physical',
                'tasks'       => [
                    ['title' => 'Exercise or walk for 20 minutes three times', 'visible' => true,  'done' => true],
                    ['title' => 'Cook a healthy meal with carer', 'visible' => true,  'done' => false],
                    ['title' => 'Drink water instead of fizzy drinks for a week', 'visible' => true,  'done' => false],
                ],
            ],
            [
                'title'       => 'Safety planning',
                'description' => 'Identify trusted people and safe spaces to use in difficult situations.',
                'domain'      => 'Safety',
                'tasks'       => [
                    ['title' => 'List three trusted adults I can contact', 'visible' => true,  'done' => true],
                    ['title' => 'Practise the safety plan with my carer', 'visible' => true,  'done' => true],
                    ['title' => 'Review safety plan with social worker', 'visible' => false, 'done' => false],
                ],
            ],
        ];

        $statuses = ['in_progress', 'in_progress', 'in_progress', 'completed', 'pending'];

        foreach ($cases->take(10) as $caseIndex => $case) {
            $template = $goalTemplates[$caseIndex % count($goalTemplates)];
            $domainId = $domains[$template['domain']] ?? null;
            $status   = $statuses[$caseIndex % count($statuses)];

            // Create goal
            $goalId = DB::table('goals')->insertGetId([
                'title'            => $template['title'],
                'description'      => $template['description'],
                'source_domain_id' => $domainId,
                'suggested_at'     => $now->copy()->subDays(rand(10, 30)),
                'approved_by'      => $swId,
                'approved_at'      => $now->copy()->subDays(rand(5, 15)),
                'created_at'       => $now->copy()->subDays(rand(10, 30)),
                'updated_at'       => $now,
            ]);

            // Create case_goal
            $caseGoalId = DB::table('case_goals')->insertGetId([
                'case_file_id'     => $case->id,
                'goal_id'          => $goalId,
                'status'           => $status,
                'due_date'         => $now->copy()->addDays(rand(7, 45))->toDateString(),
                'suggested_by'     => $swId,
                'child_visible'    => 1,
                'child_accepted_at'=> $status !== 'pending' ? $now->copy()->subDays(rand(3, 10)) : null,
                'created_at'       => $now->copy()->subDays(rand(5, 20)),
                'updated_at'       => $now,
            ]);

            // Create tasks
            foreach ($template['tasks'] as $taskDef) {
                $completedAt = $taskDef['done'] ? $now->copy()->subDays(rand(1, 5)) : null;
                DB::table('tasks')->insert([
                    'title'         => $taskDef['title'],
                    'description'   => null,
                    'ai_suggested'  => 0,
                    'child_visible' => $taskDef['visible'] ? 1 : 0,
                    'case_goal_id'  => $caseGoalId,
                    'completed_at'  => $completedAt,
                    'completed_by'  => $completedAt ? $case->young_person_id : null,
                    'created_at'    => $now->copy()->subDays(rand(5, 15)),
                    'updated_at'    => $now,
                ]);
            }
        }

        // ── 3. PLACEMENTS WITH GEOCOORDINATES ─────────────────────────────────
        $this->command->info('Seeding placements with coordinates...');

        // Real Dublin/Ireland coordinates for a realistic map
        $placementData = [
            ['location' => 'Rathmines, Dublin 6',    'lat' => 53.3235, 'lng' => -6.2635, 'type' => 'Long-Term Fostering'],
            ['location' => 'Clontarf, Dublin 3',     'lat' => 53.3644, 'lng' => -6.1949, 'type' => 'Short-Term Fostering'],
            ['location' => 'Tallaght, Dublin 24',    'lat' => 53.2886, 'lng' => -6.3544, 'type' => 'Emergency Fostering'],
            ['location' => 'Swords, Co. Dublin',     'lat' => 53.4597, 'lng' => -6.2181, 'type' => 'Kinship Care'],
            ['location' => 'Bray, Co. Wicklow',      'lat' => 53.2009, 'lng' => -6.1109, 'type' => 'Respite Care'],
            ['location' => 'Lucan, Co. Dublin',      'lat' => 53.3570, 'lng' => -6.4496, 'type' => 'Long-Term Fostering'],
            ['location' => 'Malahide, Co. Dublin',   'lat' => 53.4508, 'lng' => -6.1548, 'type' => 'Supported Lodgings'],
            ['location' => 'Finglas, Dublin 11',     'lat' => 53.3891, 'lng' => -6.2989, 'type' => 'Short-Term Fostering'],
            ['location' => 'Blackrock, Co. Dublin',  'lat' => 53.3016, 'lng' => -6.1778, 'type' => 'Long-Term Fostering'],
            ['location' => 'Blanchardstown, Dublin 15', 'lat' => 53.3893, 'lng' => -6.3768, 'type' => 'Emergency Fostering'],
            ['location' => 'Dundrum, Dublin 14',     'lat' => 53.2921, 'lng' => -6.2441, 'type' => 'Kinship Care'],
            ['location' => 'Portmarnock, Co. Dublin','lat' => 53.4219, 'lng' => -6.1378, 'type' => 'Respite Care'],
        ];

        $statuses = ['active', 'active', 'active', 'active', 'under_review', 'active', 'active', 'inactive', 'active', 'active', 'under_review', 'active'];

        foreach ($cases as $caseIdx => $case) {
            $pd       = $placementData[$caseIdx % count($placementData)];
            $statusP  = $statuses[$caseIdx % count($statuses)];
            $capacity = rand(1, 3);
            $occupancy= $statusP === 'inactive' ? 0 : rand(1, $capacity);

            // Check if placement already exists for this case
            $exists = DB::table('placements')->where('case_file_id', $case->id)->exists();
            if ($exists) {
                // Update with coordinates
                DB::table('placements')
                    ->where('case_file_id', $case->id)
                    ->update([
                        'latitude'  => $pd['lat'] + (rand(-50, 50) / 10000),
                        'longitude' => $pd['lng'] + (rand(-50, 50) / 10000),
                        'type'      => $pd['type'],
                        'status'    => $statusP,
                        'capacity'  => $capacity,
                        'current_occupancy' => $occupancy,
                    ]);
            } else {
                DB::table('placements')->insert([
                    'case_file_id'      => $case->id,
                    'carer_id'          => $carerId,
                    'type'              => $pd['type'],
                    'location'          => $pd['location'],
                    'capacity'          => $capacity,
                    'current_occupancy' => $occupancy,
                    'status'            => $statusP,
                    'latitude'          => $pd['lat'] + (rand(-50, 50) / 10000),
                    'longitude'         => $pd['lng'] + (rand(-50, 50) / 10000),
                    'start_date'        => $now->copy()->subMonths(rand(1, 12))->toDateString(),
                    'notes'             => null,
                    'created_at'        => $now,
                    'updated_at'        => $now,
                ]);
            }
        }

        // ── 4. SUPPORT REQUESTS ───────────────────────────────────────────────
        $this->command->info('Seeding support requests...');

        foreach ($youngPersonIds->take(4) as $ypId) {
            $alreadyExists = DB::table('support_requests')
                ->where('child_id', $ypId)
                ->where('status', 'open')
                ->exists();

            if (! $alreadyExists) {
                DB::table('support_requests')->insert([
                    'user_id'    => $ypId,
                    'child_id'   => $ypId,
                    'carer_id'   => $carerId,
                    'status'     => 'open',
                    'message'    => 'I need support.',
                    'created_at' => $now->copy()->subDays(rand(1, 7)),
                    'updated_at' => $now,
                ]);
            }
        }

        // ── 5. DIARY ENTRIES (young person view) ──────────────────────────────
        $this->command->info('Seeding diary entries...');

        $diaryEntries = [
            ['title' => 'Had a good day at school', 'mood' => 'happy',   'content' => 'Made a new friend in art class today. Feeling positive.'],
            ['title' => 'Feeling worried',           'mood' => 'anxious', 'content' => 'Not sure about the upcoming review meeting. Hope it goes okay.'],
            ['title' => 'Completed my goal task',    'mood' => 'proud',   'content' => 'I finished my morning checklist for the fifth day in a row!'],
            ['title' => 'Tough evening',             'mood' => 'sad',     'content' => 'Miss my family tonight. Felt lonely after dinner.'],
            ['title' => 'Went for a walk',           'mood' => 'calm',    'content' => 'Went to the park with my carer. It really helped clear my head.'],
        ];

        foreach ($youngPersonIds->take(3) as $ypId) {
            foreach ($diaryEntries as $idx => $entry) {
                $alreadyExists = DB::table('diary_entries')
                    ->where('user_id', $ypId)
                    ->where('title', $entry['title'])
                    ->exists();

                if (! $alreadyExists) {
                    DB::table('diary_entries')->insert([
                        'user_id'    => $ypId,
                        'title'      => $entry['title'],
                        'content'    => $entry['content'],
                        'mood'       => $entry['mood'],
                        'private'    => $idx === 3 ? 1 : 0,
                        'created_at' => $now->copy()->subDays(rand(1, 14)),
                        'updated_at' => $now,
                    ]);
                }
            }
        }

        // ── 6. ADDITIONAL NOTIFICATIONS ───────────────────────────────────────
        $this->command->info('Seeding additional notifications...');

        $extraNotifs = [
            ['type' => 'goal_completed',    'summary' => 'Eoin Fitzpatrick completed goal: Improve school attendance.'],
            ['type' => 'support_request',   'summary' => 'Caoimhe Ryan has requested support.'],
            ['type' => 'wellbeing_concern', 'summary' => '2 domain concern(s) from Darragh O\'Brien\'s check.'],
            ['type' => 'overdue_wellbeing', 'summary' => 'No wellbeing check for Cathal Quinn in 35 days.'],
            ['type' => 'document_uploaded', 'summary' => 'Carer uploaded "Medical Report - April 2026"'],
        ];

        $caseIdList = $cases->pluck('id')->toArray();

        foreach ($extraNotifs as $i => $notif) {
            DB::table('notifications')->insert([
                'id'              => \Illuminate\Support\Str::uuid(),
                'type'            => 'App\\Notifications\\CareHubNotification',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id'   => $swId,
                'data'            => json_encode([
                    'type'         => $notif['type'],
                    'summary'      => $notif['summary'],
                    'case_file_id' => $caseIdList[$i % count($caseIdList)],
                ]),
                'read_at'    => null,
                'created_at' => $now->copy()->subHours(rand(1, 72)),
                'updated_at' => $now,
            ]);
        }

        // ── 7. APPOINTMENTS ───────────────────────────────────────────────────
        $this->command->info('Seeding appointments...');

        $appointmentTemplates = [
            ['title' => 'Monthly review meeting',        'location' => 'HSE Office, Merrion Square, Dublin 2', 'duration' => 60],
            ['title' => 'Wellbeing check-in',            'location' => 'Placement address',                    'duration' => 45],
            ['title' => 'School liaison meeting',        'location' => 'School office',                        'duration' => 30],
            ['title' => 'Carer support session',         'location' => 'HSE Office, Tallaght',                 'duration' => 60],
            ['title' => 'LAC review',                    'location' => 'HSE Conference Room, Naas Road',       'duration' => 90],
            ['title' => 'Medical appointment follow-up', 'location' => 'Children\'s Health Ireland, Crumlin',  'duration' => 45],
            ['title' => 'Placement stability meeting',   'location' => 'Teams / Remote',                       'duration' => 60],
        ];

        $apptCount = 0;
        foreach ($cases as $caseIdx => $case) {
            // 1 upcoming appointment per case (spread over next 14 days)
            $template  = $appointmentTemplates[$caseIdx % count($appointmentTemplates)];
            $daysAhead = ($caseIdx % 14) + 1;
            $hour      = 9 + ($caseIdx % 7);
            $startTime = $now->copy()->addDays($daysAhead)->setHour($hour)->setMinute(0)->setSecond(0);

            $alreadyExists = DB::table('appointments')
                ->where('case_file_id', $case->id)
                ->where('start_time', '>=', $now)
                ->exists();

            if (! $alreadyExists) {
                DB::table('appointments')->insert([
                    'case_file_id' => $case->id,
                    'created_by'   => $swId,
                    'title'        => $template['title'],
                    'description'  => 'Scheduled review appointment for case file.',
                    'location'     => $template['location'],
                    'start_time'   => $startTime,
                    'end_time'     => $startTime->copy()->addMinutes($template['duration']),
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ]);
                $apptCount++;
            }

            // 1 past appointment per case (within last 30 days)
            $pastDays   = ($caseIdx % 25) + 5;
            $pastStart  = $now->copy()->subDays($pastDays)->setHour(10 + ($caseIdx % 5))->setMinute(0)->setSecond(0);
            $pastTemplate = $appointmentTemplates[($caseIdx + 2) % count($appointmentTemplates)];

            DB::table('appointments')->insert([
                'case_file_id' => $case->id,
                'created_by'   => $swId,
                'title'        => $pastTemplate['title'],
                'description'  => 'Completed appointment.',
                'location'     => $pastTemplate['location'],
                'start_time'   => $pastStart,
                'end_time'     => $pastStart->copy()->addMinutes($pastTemplate['duration']),
                'created_at'   => $pastStart,
                'updated_at'   => $pastStart,
            ]);
            $apptCount++;
        }

        // ── 8. DOCUMENTS ─────────────────────────────────────────────────────
        $this->command->info('Seeding documents...');

        $documentTemplates = [
            ['title' => 'Placement Agreement',         'file' => 'documents/placement_agreement.pdf',        'uploader' => 'carer'],
            ['title' => 'LAC Review Report',           'file' => 'documents/lac_review_report.pdf',          'uploader' => 'sw'],
            ['title' => 'Medical Summary',             'file' => 'documents/medical_summary.pdf',            'uploader' => 'sw'],
            ['title' => 'School Report - Term 1',      'file' => 'documents/school_report_term1.pdf',        'uploader' => 'sw'],
            ['title' => 'Risk Assessment',             'file' => 'documents/risk_assessment.pdf',            'uploader' => 'sw'],
            ['title' => 'Consent Form',                'file' => 'documents/consent_form.pdf',               'uploader' => 'carer'],
            ['title' => 'Care Plan',                   'file' => 'documents/care_plan.pdf',                  'uploader' => 'sw'],
            ['title' => 'Carer Reference Check',       'file' => 'documents/carer_reference_check.pdf',     'uploader' => 'sw'],
        ];

        $docCount = 0;
        foreach ($cases as $caseIdx => $case) {
            // 2 documents per case
            for ($d = 0; $d < 2; $d++) {
                $template   = $documentTemplates[($caseIdx * 2 + $d) % count($documentTemplates)];
                $uploaderId = $template['uploader'] === 'carer' ? $carerId : $swId;
                $uploadedAt = $now->copy()->subDays(rand(1, 30));

                $alreadyExists = DB::table('documents')
                    ->where('case_file_id', $case->id)
                    ->where('title', $template['title'])
                    ->exists();

                if (! $alreadyExists) {
                    DB::table('documents')->insert([
                        'case_file_id' => $case->id,
                        'title'         => $template['title'],
                        'file_path'    => $template['file'],
                        'uploaded_by'  => $uploaderId,
                        'created_at'   => $uploadedAt,
                        'updated_at'   => $uploadedAt,
                    ]);
                    $docCount++;
                }
            }
        }

        $this->command->info('Done. Summary:');
        $this->command->info('  Messages: ' . count($messagePairs) . ' threads × 7 messages');
        $this->command->info('  Goals + tasks: ' . $cases->take(10)->count() . ' goals seeded');
        $this->command->info('  Placements: coordinates added/updated for ' . $cases->count() . ' cases');
        $this->command->info('  Support requests: 4');
        $this->command->info('  Diary entries: ' . (3 * count($diaryEntries)));
        $this->command->info('  Notifications: ' . count($extraNotifs) . ' added');
        $this->command->info('  Appointments: ' . $apptCount . ' seeded (' . $cases->count() . ' upcoming + ' . $cases->count() . ' past)');
        $this->command->info('  Documents: ' . $docCount . ' seeded (2 per case)');
    }
}