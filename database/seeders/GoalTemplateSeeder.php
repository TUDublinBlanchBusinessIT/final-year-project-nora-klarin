<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * GoalTemplateSeeder
 *
 * Seeds predefined goal templates and maps them to the tags that trigger them.
 *
 * Each template represents a structured intervention pattern. When the
 * system detects a negative pattern via tags or domain scores, it matches
 * to a template and surfaces a suggestion to the social worker for approval.
 *
 * This implements the NCB (2017) principle that assessment value is
 * realised through intervention — the template system is the bridge
 * between measurement and action.
 */
class GoalTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedTemplates();
        $this->seedTagMappings();
    }

    private function seedTemplates(): void
    {
        // Keyed by a slug for easy reference in tag mappings below
        $templates = [
            'sleep_improvement' => [
                'title'       => 'Sleep improvement',
                'description' => 'Support the young person in establishing consistent sleep routines, '
                               . 'reducing screen time before bed, and addressing barriers to restful sleep.',
                'domain'      => 'Physical',
            ],
            'emotional_regulation' => [
                'title'       => 'Emotional regulation skills',
                'description' => 'Build the young person\'s capacity to identify, express, and manage '
                               . 'difficult emotions through coping strategies and trusted adult support.',
                'domain'      => 'Emotional',
            ],
            'anxiety_support' => [
                'title'       => 'Anxiety management support',
                'description' => 'Connect the young person with appropriate anxiety management strategies '
                               . 'and professional support where indicated.',
                'domain'      => 'Emotional',
            ],
            'self_esteem_building' => [
                'title'       => 'Self-esteem and confidence building',
                'description' => 'Support the young person in recognising their strengths, achievements, '
                               . 'and positive qualities through structured activities and positive reinforcement.',
                'domain'      => 'Emotional',
            ],
            'social_engagement' => [
                'title'       => 'Social engagement and friendship',
                'description' => 'Support the young person in building and maintaining positive peer '
                               . 'relationships through structured social activities and social skills development.',
                'domain'      => 'Social',
            ],
            'peer_conflict_resolution' => [
                'title'       => 'Peer conflict resolution',
                'description' => 'Help the young person develop strategies for managing conflict with '
                               . 'peers constructively and safely.',
                'domain'      => 'Social',
            ],
            'family_relationship_support' => [
                'title'       => 'Family relationship support',
                'description' => 'Facilitate positive communication and relationship-building between '
                               . 'the young person and their family or placement carers.',
                'domain'      => 'Social',
            ],
            'school_attendance' => [
                'title'       => 'School attendance improvement',
                'description' => 'Identify and address barriers to school attendance through '
                               . 'collaborative planning with school, carers, and the young person.',
                'domain'      => 'Education',
            ],
            'school_engagement' => [
                'title'       => 'School engagement and learning',
                'description' => 'Support the young person\'s engagement with learning through '
                               . 'identifying interests, reducing academic anxiety, and building school '
                               . 'relationships.',
                'domain'      => 'Education',
            ],
            'physical_activity' => [
                'title'       => 'Physical activity and exercise',
                'description' => 'Increase the young person\'s participation in physical activity '
                               . 'through identifying enjoyable activities and reducing barriers to access.',
                'domain'      => 'Physical',
            ],
            'healthy_eating' => [
                'title'       => 'Healthy eating habits',
                'description' => 'Support the young person in developing a positive and healthy '
                               . 'relationship with food and regular mealtimes.',
                'domain'      => 'Physical',
            ],
            'conduct_support' => [
                'title'       => 'Behaviour and conduct support',
                'description' => 'Work with the young person to identify triggers for challenging '
                               . 'behaviour and develop positive coping and self-regulation strategies.',
                'domain'      => 'Behavioural',
            ],
            'placement_stability' => [
                'title'       => 'Placement transition support',
                'description' => 'Support the young person in managing placement-related anxiety '
                               . 'and building a sense of security and belonging in their current placement.',
                'domain'      => 'Safety',
            ],
            'identity_support' => [
                'title'       => 'Identity and belonging',
                'description' => 'Support the young person in exploring their identity, cultural '
                               . 'background, and sense of belonging in a safe and affirming way.',
                'domain'      => 'Social',
            ],
            'concentration_support' => [
                'title'       => 'Focus and concentration support',
                'description' => 'Identify strategies to support the young person\'s concentration '
                               . 'and attention in school and daily life, including referral for assessment '
                               . 'where appropriate.',
                'domain'      => 'Education',
            ],
        ];

        foreach ($templates as $slug => $data) {
            $domainId = DB::table('domains')
                ->where('name', $data['domain'])
                ->value('id');

            DB::table('goal_templates')->updateOrInsert(
                ['title' => $data['title']],
                [
                    'description'      => $data['description'],
                    'source_domain_id' => $domainId,
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]
            );
        }
    }

    private function seedTagMappings(): void
    {
        // Format: [tag_name, template_title, trigger_threshold]
        // trigger_threshold: suggest goal when wb_score drops below this value
        $mappings = [
            // Sleep
            ['sleep_disruption',     'Sleep improvement',                    45],

            // Emotional
            ['anxiety',              'Anxiety management support',            45],
            ['anxiety',              'Emotional regulation skills',           50],
            ['depression_signs',     'Emotional regulation skills',           40],
            ['depression_signs',     'Self-esteem and confidence building',   40],
            ['emotional_dysregulation', 'Emotional regulation skills',        50],
            ['low_self_esteem',      'Self-esteem and confidence building',   45],
            ['trauma_response',      'Emotional regulation skills',           50],
            ['placement_anxiety',    'Placement transition support',          45],

            // Social
            ['social_isolation',     'Social engagement and friendship',      45],
            ['peer_conflict',        'Peer conflict resolution',              45],
            ['peer_conflict',        'Social engagement and friendship',      40],
            ['family_conflict',      'Family relationship support',           45],
            ['attachment_issues',    'Family relationship support',           50],
            ['attachment_issues',    'Placement transition support',          45],
            ['identity',             'Identity and belonging',                45],
            ['positive_relationships','Social engagement and friendship',     50],

            // Education
            ['school_disengagement', 'School engagement and learning',        45],
            ['attendance_concern',   'School attendance improvement',         40],
            ['concentration',        'Focus and concentration support',       45],
            ['concentration',        'School engagement and learning',        45],

            // Physical
            ['physical_inactivity',  'Physical activity and exercise',        45],
            ['poor_nutrition',       'Healthy eating habits',                 45],
            ['disordered_eating',    'Healthy eating habits',                 35],

            // Behavioural
            ['conduct_issues',       'Behaviour and conduct support',         45],
            ['substance_use',        'Behaviour and conduct support',         35],
        ];

        foreach ($mappings as [$tagName, $templateTitle, $threshold]) {
            $tagId = DB::table('tags')->where('name', $tagName)->value('id');
            $templateId = DB::table('goal_templates')->where('title', $templateTitle)->value('id');

            if (!$tagId || !$templateId) {
                $this->command->warn("Skipping mapping: tag={$tagName}, template={$templateTitle} — not found.");
                continue;
            }

            DB::table('tag_goal_templates')->updateOrInsert(
                ['tag_id' => $tagId, 'goal_template_id' => $templateId],
                [
                    'trigger_threshold' => $threshold,
                    'created_at'        => now(),
                ]
            );
        }
    }
}
