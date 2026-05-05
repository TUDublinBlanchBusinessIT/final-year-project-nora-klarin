<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // ── Ensure SW (id 7) and carer (id 1) exist ──────────────────────────
        $swId    = 7;
        $carerId = 1;

        // ── Domain IDs (fetch from DB — don't assume order) ──────────────────
        $domains = DB::table('domains')->pluck('id', 'name');

        // Normalise keys to lowercase
        $domainMap = [];
        foreach ($domains as $name => $id) {
            $domainMap[strtolower(trim($name))] = $id;
        }

        $domainKeys = [
            'emotional'        => $domainMap['emotional']        ?? null,
            'behavioural'      => $domainMap['behavioural']      ?? null,
            'social'           => $domainMap['social']           ?? null,
            'physical'         => $domainMap['physical']         ?? null,
            'education'        => $domainMap['education']        ?? null,
            'safety'           => $domainMap['safety']           ?? null,
            'life satisfaction'=> $domainMap['life satisfaction']?? null,
        ];

        // Filter out nulls
        $domainKeys = array_filter($domainKeys);

        // ── Young people ──────────────────────────────────────────────────────
        $youngPeople = [
            ['name' => 'Aoife Murphy',      'dob' => '2010-03-14', 'risk' => 'high',   'scores' => [45, 30, 50, 40, 55, 20, 35]],
            ['name' => 'Ciarán Walsh',      'dob' => '2009-07-22', 'risk' => 'medium', 'scores' => [65, 70, 60, 75, 68, 72, 64]],
            ['name' => 'Saoirse Kelly',     'dob' => '2011-11-05', 'risk' => 'low',    'scores' => [85, 88, 80, 90, 82, 95, 87]],
            ['name' => 'Darragh O\'Brien',  'dob' => '2008-02-19', 'risk' => 'high',   'scores' => [38, 42, 35, 30, 48, 25, 40]],
            ['name' => 'Niamh Brennan',     'dob' => '2010-09-30', 'risk' => 'medium', 'scores' => [60, 55, 65, 58, 70, 62, 59]],
            ['name' => 'Eoin Fitzpatrick',  'dob' => '2009-04-17', 'risk' => 'low',    'scores' => [78, 82, 75, 88, 80, 85, 76]],
            ['name' => 'Caoimhe Ryan',      'dob' => '2012-06-08', 'risk' => 'high',   'scores' => [30, 25, 40, 35, 28, 18, 32]],
            ['name' => 'Oisín Gallagher',   'dob' => '2008-12-01', 'risk' => 'medium', 'scores' => [55, 60, 52, 65, 58, 68, 53]],
            ['name' => 'Roisín McCarthy',   'dob' => '2011-08-14', 'risk' => 'low',    'scores' => [88, 92, 85, 94, 90, 96, 89]],
            ['name' => 'Fionn Doyle',       'dob' => '2009-01-28', 'risk' => 'medium', 'scores' => [62, 58, 68, 70, 65, 74, 60]],
            ['name' => 'Méabh Collins',     'dob' => '2010-05-12', 'risk' => 'high',   'scores' => [42, 38, 45, 32, 50, 22, 38]],
            ['name' => 'Tadhg O\'Connor',   'dob' => '2008-10-03', 'risk' => 'low',    'scores' => [80, 85, 78, 88, 84, 90, 82]],
            ['name' => 'Ailbhe Nolan',      'dob' => '2011-03-25', 'risk' => 'medium', 'scores' => [58, 62, 55, 68, 60, 72, 57]],
            ['name' => 'Cathal Quinn',      'dob' => '2009-06-16', 'risk' => 'high',   'scores' => [35, 28, 42, 30, 45, 15, 33]],
            ['name' => 'Síofra Burke',      'dob' => '2012-09-20', 'risk' => 'low',    'scores' => [82, 87, 79, 91, 85, 93, 84]],
        ];

        $placementTypes = [
            'Emergency Fostering', 'Short-Term Fostering', 'Long-Term Fostering',
            'Kinship Care', 'Respite Care', 'Supported Lodgings',
        ];

        $locations = [
            'Dublin 1', 'Dublin 4', 'Dublin 6', 'Dublin 8', 'Dublin 12',
            'Dún Laoghaire', 'Bray', 'Swords', 'Tallaght', 'Blanchardstown',
            'Clondalkin', 'Lucan', 'Malahide', 'Finglas', 'Rathfarnham',
        ];

        $caseRefs = [];

        foreach ($youngPeople as $i => $yp) {

            // ── Create young person user ──────────────────────────────────────
            $email = strtolower(
                preg_replace('/[^a-z]/i', '', explode(' ', $yp['name'])[0])
                . '.' .
                preg_replace('/[^a-z]/i', '', explode(' ', $yp['name'])[1])
            ) . $i . '@carehub.test';

            $ypId = DB::table('users')->insertGetId([
                'name'       => $yp['name'],
                'email'      => $email,
                'password'   => Hash::make('password'),
                'role'       => 'young_person',
                'theme'      => 'calm',
                'chatbot_name' => 'CareHub Assistant',
                'dashboard_layout' => 'standard',
                'dob'        => $yp['dob'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            // ── Create case file ──────────────────────────────────────────────
            $caseRef = 'CH-2024-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT);
            $openedAt = $now->copy()->subMonths(rand(3, 18));

            $caseId = DB::table('case_files')->insertGetId([
                'young_person_id'  => $ypId,
                'status'           => 'open',
                'risk_level'       => $yp['risk'],
                'case_reference'   => $caseRef,
                'placement_type'   => $placementTypes[$i % count($placementTypes)],
                'placement_location' => $locations[$i],
                'opened_at'        => $openedAt,
                'last_reviewed_at' => $now->copy()->subDays(rand(1, 14)),
                'summary'          => 'Case opened following referral. Young person is currently in ' . $placementTypes[$i % count($placementTypes)] . ' placement.',
                'created_at'       => $openedAt,
                'updated_at'       => $now,
            ]);

            $caseRefs[] = ['case_id' => $caseId, 'yp_id' => $ypId, 'risk' => $yp['risk']];

            // ── Assign SW and carer to case ───────────────────────────────────
            DB::table('case_user')->insert([
                [
                    'case_file_id' => $caseId,
                    'user_id'      => $swId,
                    'role'         => 'social_worker',
                    'assigned_at'  => $openedAt,
                    'created_at'   => $openedAt,
                    'updated_at'   => $now,
                ],
                [
                    'case_file_id' => $caseId,
                    'user_id'      => $carerId,
                    'role'         => 'carer',
                    'assigned_at'  => $openedAt,
                    'created_at'   => $openedAt,
                    'updated_at'   => $now,
                ],
                [
                    'case_file_id' => $caseId,
                    'user_id'      => $ypId,
                    'role'         => 'young_person',
                    'assigned_at'  => $openedAt,
                    'created_at'   => $openedAt,
                    'updated_at'   => $now,
                ],
            ]);

            // ── Placement ─────────────────────────────────────────────────────
            DB::table('placements')->insert([
                'case_file_id'      => $caseId,
                'carer_id'          => $carerId,
                'type'              => $placementTypes[$i % count($placementTypes)],
                'location'          => $locations[$i],
                'capacity'          => rand(1, 3),
                'current_occupancy' => 1,
                'status'            => 'active',
                'start_date'        => $openedAt->toDateString(),
                'notes'             => null,
                'created_at'        => $openedAt,
                'updated_at'        => $now,
            ]);

            // ── Medical info ──────────────────────────────────────────────────
            $conditions = ['None recorded', 'Asthma', 'ADHD', 'Anxiety', 'Dyslexia'];
            DB::table('medical_infos')->insert([
                'case_file_id' => $caseId,
                'condition'    => $conditions[$i % count($conditions)],
                'notes'        => null,
                'created_at'   => $openedAt,
                'updated_at'   => $now,
            ]);

            // ── Education info ────────────────────────────────────────────────
            $schools = [
                'St. Patrick\'s CBS', 'Mercy Secondary School', 'Coláiste Bríde',
                'Ringsend College', 'Cabinteely Community School', 'The High School',
                'Templeogue College', 'Clondalkin Community School', 'Fingal Community College',
                'Mount Anville Secondary School', 'Loreto College', 'Gonzaga College',
                'Dundrum College', 'Tallaght Community School', 'Lucan Community College',
            ];
            DB::table('education_infos')->insert([
                'case_file_id' => $caseId,
                'school_name'  => $schools[$i],
                'grade'        => 'Year ' . rand(1, 6),
                'notes'        => null,
                'created_at'   => $openedAt,
                'updated_at'   => $now,
            ]);

            // ── Appointment ───────────────────────────────────────────────────
            $apptStart = $now->copy()->addDays(rand(1, 14))->setHour(rand(9, 15))->setMinute(0);
            DB::table('appointments')->insert([
                'case_file_id' => $caseId,
                'created_by'   => $swId,
                'title'        => 'Review meeting — ' . $yp['name'],
                'description'  => 'Scheduled review appointment.',
                'location'     => 'HSE Office, Dublin',
                'start_time'   => $apptStart,
                'end_time'     => $apptStart->copy()->addHour(),
                'created_at'   => $now,
                'updated_at'   => $now,
            ]);

            // ── Wellbeing checks (3 per case, spread over time) ───────────────
            $scores  = $yp['scores'];  // 7 domain scores
            $domKeys = array_keys($domainKeys);

            for ($c = 0; $c < 3; $c++) {
                $checkDate = $now->copy()->subDays((2 - $c) * 25 + rand(0, 5));

                // Add some variance per check
                $variance = ($c === 0) ? -15 : ($c === 1 ? -5 : 0);
                $adjScores = array_map(fn($s) => max(5, min(100, $s + $variance + rand(-5, 5))), $scores);

                $overallScore = round(array_sum($adjScores) / count($adjScores));
                $riskLevel = match(true) {
                    $overallScore < 35 => 'critical',
                    $overallScore < 50 => 'high',
                    $overallScore < 65 => 'moderate',
                    default            => 'low',
                };

                $checkId = DB::table('wellbeing_checks')->insertGetId([
                    'young_person_id' => $ypId,
                    'case_file_id'    => $caseId,
                    'overall_score'   => $overallScore,
                    'check_type'      => $c === 0 ? 'intake' : 'scheduled',
                    'game_mode'       => 'slider',
                    'submitted_by'    => $ypId,
                    'risk_level'      => $riskLevel,
                    'completed_at'    => $checkDate,
                    'created_at'      => $checkDate,
                    'updated_at'      => $checkDate,
                ]);

                // ── Domain scores ─────────────────────────────────────────────
                foreach ($domainKeys as $domainName => $domainId) {
                    $idx   = array_search($domainName, $domKeys);
                    $score = $adjScores[$idx !== false ? $idx : 0];
                    $riskScore = round((100 - $score) * 1.0, 2);

                    DB::table('wellbeing_domain_scores')->insert([
                        'wellbeing_check_id' => $checkId,
                        'domain_id'          => $domainId,
                        'average_score'      => $score,
                        'risk_score'         => $riskScore,
                        'created_at'         => $checkDate,
                        'updated_at'         => $checkDate,
                    ]);
                }

                // ── Alerts for high-risk cases on latest check ────────────────
                if ($c === 2 && $yp['risk'] === 'high') {
                    DB::table('alerts')->insert([
                        'wellbeing_check_id' => $checkId,
                        'young_person_id'    => $ypId,
                        'alert_type'         => 'tag_override',
                        'severity'           => 'critical',
                        'message'            => 'Safeguarding concern: "self_harm" tag triggered on "Have you ever hurt yourself on purpose?" (score: ' . min(20, $adjScores[5]) . '/100).',
                        'acknowledged_at'    => null,
                        'acknowledged_by'    => null,
                        'created_at'         => $checkDate,
                        'updated_at'         => $checkDate,
                    ]);
                }
            }
        }

        // ── Support requests (a few, for demo) ───────────────────────────────
        foreach (array_slice($caseRefs, 0, 3) as $cr) {
            DB::table('support_requests')->insert([
                'user_id'    => $cr['yp_id'],
                'child_id'   => $cr['yp_id'],
                'carer_id'   => $carerId,
                'status'     => 'open',
                'message'    => 'I need support.',
                'created_at' => $now->copy()->subDays(rand(1, 5)),
                'updated_at' => $now,
            ]);
        }

        // ── Notifications for SW (demo bell content) ──────────────────────────
        $notifTypes = [
            ['type' => 'wellbeing_completed', 'summary' => 'Aoife Murphy completed a wellbeing check.', 'case_file_id' => $caseRefs[0]['case_id']],
            ['type' => 'document_uploaded',   'summary' => 'Carer uploaded "Placement Agreement.pdf"',  'case_file_id' => $caseRefs[1]['case_id']],
            ['type' => 'goal_completed',       'summary' => 'Saoirse Kelly completed goal: Build a daily routine.', 'case_file_id' => $caseRefs[2]['case_id']],
            ['type' => 'overdue_wellbeing',    'summary' => 'No wellbeing check for Darragh O\'Brien in 32 days.', 'case_file_id' => $caseRefs[3]['case_id']],
            ['type' => 'support_request',      'summary' => 'Niamh Brennan has requested support.',     'case_file_id' => $caseRefs[4]['case_id']],
        ];

        foreach ($notifTypes as $n) {
            DB::table('notifications')->insert([
                'id'              => \Illuminate\Support\Str::uuid(),
                'type'            => 'App\\Notifications\\CareHubNotification',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id'   => $swId,
                'data'            => json_encode([
                    'type'         => $n['type'],
                    'summary'      => $n['summary'],
                    'case_file_id' => $n['case_file_id'],
                ]),
                'read_at'    => null,
                'created_at' => $now->copy()->subHours(rand(1, 48)),
                'updated_at' => $now,
            ]);
        }

        $this->command->info('Demo seeder complete.');
        $this->command->info('Created 15 young people, cases, checks, domain scores, alerts, and notifications.');
        $this->command->info('All user passwords: password');
    }
}
