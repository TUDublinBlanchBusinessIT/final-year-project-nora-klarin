<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DomainAndTagSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedDomains();
        $this->seedTags();
    }

    private function seedDomains(): void
{
    DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    DB::table('domains')->truncate();
    DB::statement('SET FOREIGN_KEY_CHECKS=1;');

    $domains = [
        ['name' => 'Emotional', 'description' => '...'],
        ['name' => 'Behavioural', 'description' => '...'],
        ['name' => 'Social', 'description' => '...'],
        ['name' => 'Physical', 'description' => '...'],
        ['name' => 'Education', 'description' => '...'],
        ['name' => 'Safety', 'description' => '...'],
        ['name' => 'Life Satisfaction', 'description' => '...'],
    ];

    foreach ($domains as $domain) {
        DB::table('domains')->insert([
            ...$domain,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
    private function seedTags(): void
    {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    DB::table('tags')->truncate();  // was ->delete(), change to truncate
    DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $tags = [
            ['self_harm',              'safeguarding', true,  35],
            ['suicidal_ideation',      'safeguarding', true,  40],
            ['abuse_physical',         'safeguarding', true,  40],
            ['abuse_emotional',        'safeguarding', true,  40],
            ['abuse_sexual',           'safeguarding', true,  40],
            ['neglect',                'safeguarding', true,  40],
            ['running_away',           'safeguarding', true,  35],
            ['exploitation_risk',      'safeguarding', true,  35],

            ['anxiety',                'clinical',     false, null],
            ['depression_signs',       'clinical',     false, null],
            ['trauma_response',        'clinical',     false, null],
            ['attachment_issues',      'clinical',     false, null],
            ['emotional_dysregulation','clinical',     false, null],
            ['substance_use',          'clinical',     false, null],
            ['disordered_eating',      'clinical',     false, null],
            ['stress',                 'clinical',     false, null],

            ['sleep_disruption',       'goal_mapping', false, null],
            ['low_self_esteem',        'goal_mapping', false, null],
            ['peer_conflict',          'goal_mapping', false, null],
            ['social_isolation',       'goal_mapping', false, null],
            ['school_disengagement',   'goal_mapping', false, null],
            ['attendance_concern',     'goal_mapping', false, null],
            ['physical_inactivity',    'goal_mapping', false, null],
            ['poor_nutrition',         'goal_mapping', false, null],
            ['conduct_issues',         'goal_mapping', false, null],
            ['concentration',          'goal_mapping', false, null],
            ['emotional_regulation',   'goal_mapping', false, null],
            ['family_conflict',        'goal_mapping', false, null],
            ['placement_anxiety',      'goal_mapping', false, null],
            ['identity',               'goal_mapping', false, null],
            ['positive_relationships', 'goal_mapping', false, null],
            ['body_image',             'goal_mapping', false, null],
            ['bullying_victim',        'goal_mapping', false, null],

            ['optimism',               'pattern',      false, null],
            ['resilience',             'pattern',      false, null],
            ['belonging',              'pattern',      false, null],
            ['future_orientation',     'pattern',      false, null],
            ['physical_complaints',    'pattern',      false, null],
            ['self_efficacy',          'pattern',      false, null],
            ['life_satisfaction',      'pattern',      false, null],
        ];

        foreach ($tags as [$name, $category, $alertOverride, $alertThreshold]) {
            DB::table('tags')->insert([
                'name'            => $name,
                'category'        => $category,
                'alert_override'  => $alertOverride,
                'alert_threshold' => $alertThreshold,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
        }
    }
}