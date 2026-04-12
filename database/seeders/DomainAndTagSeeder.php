<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * DomainAndTagSeeder
 *
 * Seeds the six wellbeing domains and the full tag vocabulary.
 *
 * Domain justification:
 *   The six domains are derived primarily from the OECD Child Wellbeing
 *   Framework (OECD, 2009), which provides a validated multi-dimensional
 *   model covering health, education, social relationships, risk behaviours,
 *   and subjective wellbeing. A Safety domain is added beyond the OECD
 *   framework to reflect the specific vulnerability profile of looked-after
 *   children, consistent with the Looking After Children (LAC) framework
 *   and statutory safeguarding guidance (HM Government, 2018).
 *
 * Tag categories:
 *   safeguarding  — alert_override = true, bypasses scoring pipeline
 *   clinical      — flags response for professional review
 *   goal_mapping  — drives automated goal suggestions
 *   pattern       — longitudinal trend detection only
 */
class DomainAndTagSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedDomains();
        $this->seedTags();
    }

    private function seedDomains(): void
    {
        $domains = [
            [
                'name'        => 'Emotional',
                'description' => 'Subjective emotional experience, psychological wellbeing, and mental health. '
                               . 'Grounded in OECD subjective wellbeing dimension, WEMWBS, and Me and My Feelings '
                               . '(Deighton et al., 2014).',
            ],
            [
                'name'        => 'Behavioural',
                'description' => 'Conduct, self-regulation, attention, and risk-related behaviours. '
                               . 'Grounded in OECD risk behaviours dimension and SDQ conduct/hyperactivity '
                               . 'subscales (Goodman, 1997).',
            ],
            [
                'name'        => 'Social',
                'description' => 'Peer relationships, family relationships, loneliness, and social connectedness. '
                               . 'Grounded in OECD social relationships dimension, HBSC peer relations module, '
                               . 'and SDQ peer problems subscale.',
            ],
            [
                'name'        => 'Physical',
                'description' => 'Physical health, sleep, nutrition, and exercise. '
                               . 'Grounded in OECD health dimension and HBSC health complaints and '
                               . 'health behaviours modules.',
            ],
            [
                'name'        => 'Education',
                'description' => 'School engagement, attendance, academic confidence, and learning experience. '
                               . 'Grounded in OECD education dimension, HBSC school module, and '
                               . 'CORS school scale (Duncan et al., 2006).',
            ],
            [
                'name'        => 'Safety',
                'description' => 'Personal safety, exposure to harm, and safeguarding concerns. '
                               . 'Added beyond the OECD framework to reflect the elevated vulnerability '
                               . 'profile of looked-after children (Denlinger & Dorius, 2018), consistent '
                               . 'with the LAC framework and Working Together to Safeguard Children '
                               . '(HM Government, 2018).',
            ],
        ];

        foreach ($domains as $domain) {
            DB::table('domains')->updateOrInsert(
                ['name' => $domain['name']],
                array_merge($domain, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }

    private function seedTags(): void
    {
        // Format: name, category, alert_override, alert_threshold
        // alert_threshold: wb_score below this fires alert when alert_override = true
        $tags = [
            // ── Safeguarding tags (alert_override = true) ──────────────────
            // These bypass the scoring pipeline entirely and generate
            // an immediate critical alert regardless of overall check score.
            ['self_harm',           'safeguarding', true,  35],
            ['suicidal_ideation',   'safeguarding', true,  40],
            ['abuse_physical',      'safeguarding', true,  40],
            ['abuse_emotional',     'safeguarding', true,  40],
            ['abuse_sexual',        'safeguarding', true,  40],
            ['neglect',             'safeguarding', true,  40],
            ['running_away',        'safeguarding', true,  35],
            ['exploitation_risk',   'safeguarding', true,  35],

            // ── Clinical tags (flag for professional review) ────────────────
            // Do not generate immediate alerts but are recorded and visible
            // to social workers as requiring follow-up.
            ['anxiety',             'clinical',     false, null],
            ['depression_signs',    'clinical',     false, null],
            ['trauma_response',     'clinical',     false, null],
            ['attachment_issues',   'clinical',     false, null],
            ['emotional_dysregulation', 'clinical', false, null],
            ['substance_use',       'clinical',     false, null],
            ['disordered_eating',   'clinical',     false, null],

            // ── Goal mapping tags (drive automated goal suggestions) ────────
            ['sleep_disruption',    'goal_mapping', false, null],
            ['low_self_esteem',     'goal_mapping', false, null],
            ['peer_conflict',       'goal_mapping', false, null],
            ['social_isolation',    'goal_mapping', false, null],
            ['school_disengagement','goal_mapping', false, null],
            ['attendance_concern',  'goal_mapping', false, null],
            ['physical_inactivity', 'goal_mapping', false, null],
            ['poor_nutrition',      'goal_mapping', false, null],
            ['conduct_issues',      'goal_mapping', false, null],
            ['concentration',       'goal_mapping', false, null],
            ['emotional_regulation','goal_mapping', false, null],
            ['family_conflict',     'goal_mapping', false, null],
            ['placement_anxiety',   'goal_mapping', false, null],
            ['identity',            'goal_mapping', false, null],
            ['positive_relationships', 'goal_mapping', false, null],

            // ── Pattern tags (longitudinal trend detection only) ────────────
            // Not mapped to goals or alerts — used to detect themes
            // building across multiple checks over time.
            ['optimism',            'pattern',      false, null],
            ['resilience',          'pattern',      false, null],
            ['belonging',           'pattern',      false, null],
            ['future_orientation',  'pattern',      false, null],
            ['physical_complaints', 'pattern',      false, null],
        ];

        foreach ($tags as [$name, $category, $alertOverride, $alertThreshold]) {
            DB::table('tags')->updateOrInsert(
                ['name' => $name],
                [
                    'category'        => $category,
                    'alert_override'  => $alertOverride,
                    'alert_threshold' => $alertThreshold,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]
            );
        }
    }
}
