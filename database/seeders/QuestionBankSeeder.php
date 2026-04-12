<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionBankSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedEmotionalDomain();
        $this->seedBehaviouralDomain();
        $this->seedSocialDomain();
        $this->seedPhysicalDomain();
        $this->seedEducationDomain();
        $this->seedSafetyDomain();
    }

    private function seedEmotionalDomain(): void
    {
        $domainId = $this->domainId('Emotional');

        $questions = [
            [
                'text'             => 'How happy have you been feeling lately?',
                'response_type'    => 'emoji_scale',
                'source_framework' => 'CORS',
                'min_value'        => 0,
                'max_value'        => 4,
                'is_positive'      => true,
                'risk_weight'      => 1.0,
                'risk_level'       => 'medium',
                'age_band_min'     => 8,
                'age_band_max'     => 17,
                'tags'             => ['depression_signs', 'optimism'],
                'wordings'         => [
                    [8,  12, 'How happy have you been feeling lately?'],
                    [13, 17, 'How would you describe your overall mood over the past week?'],
                ],
            ],
            [
                'text'             => 'How often have you felt worried or anxious?',
                'response_type'    => 'likert_5',
                'source_framework' => 'M&MF',
                'min_value'        => 0,
                'max_value'        => 4,
                'is_positive'      => false,
                'risk_weight'      => 1.0,
                'risk_level'       => 'medium',
                'age_band_min'     => 8,
                'age_band_max'     => 17,
                'tags'             => ['anxiety'],
                'wordings'         => [
                    [8,  12, 'Do you ever feel worried or scared a lot?'],
                    [13, 17, 'How often have you felt anxious or nervous lately?'],
                ],
            ],
            [
                'text'             => 'How often have you felt low or sad?',
                'response_type'    => 'likert_5',
                'source_framework' => 'M&MF',
                'min_value'        => 0,
                'max_value'        => 4,
                'is_positive'      => false,
                'risk_weight'      => 2.0,
                'risk_level'       => 'high',
                'age_band_min'     => 8,
                'age_band_max'     => 17,
                'tags'             => ['depression_signs', 'emotional_regulation'],
                'wordings'         => [
                    [8,  12, 'How often do you feel sad or upset?'],
                    [13, 17, 'How often have you felt low, sad, or down lately?'],
                ],
            ],
            [
                'text'             => 'I have been feeling good about myself.',
                'response_type'    => 'likert_5',
                'source_framework' => 'WEMWBS',
                'min_value'        => 0,
                'max_value'        => 4,
                'is_positive'      => true,
                'risk_weight'      => 1.0,
                'risk_level'       => 'medium',
                'age_band_min'     => 10,
                'age_band_max'     => 17,
                'tags'             => ['low_self_esteem', 'resilience'],
                'wordings'         => [
                    [10, 13, 'I have been feeling good about who I am.'],
                    [14, 17, 'I have been feeling good about myself.'],
                ],
            ],
            [
                'text'             => 'I have been feeling optimistic about the future.',
                'response_type'    => 'likert_5',
                'source_framework' => 'WEMWBS',
                'min_value'        => 0,
                'max_value'        => 4,
                'is_positive'      => true,
                'risk_weight'      => 1.0,
                'risk_level'       => 'medium',
                'age_band_min'     => 10,
                'age_band_max'     => 17,
                'tags'             => ['future_orientation', 'optimism'],
                'wordings'         => [
                    [10, 13, 'I feel like good things are going to happen.'],
                    [14, 17, 'I have been feeling hopeful about the future.'],
                ],
            ],
            [
                'text'             => 'How easy is it for you to calm down when you feel upset?',
                'response_type'    => 'likert_5',
                'source_framework' => 'M&MF',
                'min_value'        => 0,
                'max_value'        => 4,
                'is_positive'      => true,
                'risk_weight'      => 1.0,
                'risk_level'       => 'medium',
                'age_band_min'     => 8,
                'age_band_max'     => 17,
                'tags'             => ['emotional_regulation', 'emotional_dysregulation'],
                'wordings'         => [
                    [8,  12, 'When you feel upset, how easy is it to feel better again?'],
                    [13, 17, 'How easy do you find it to calm down when you\'re feeling upset or angry?'],
                ],
            ],
            [
                'text'             => 'How often do you feel like nobody cares about you?',
                'response_type'    => 'likert_5',
                'source_framework' => 'OECD',
                'min_value'        => 0,
                'max_value'        => 4,
                'is_positive'      => false,
                'risk_weight'      => 2.0,
                'risk_level'       => 'high',
                'age_band_min'     => 8,
                'age_band_max'     => 17,
                'tags'             => ['depression_signs', 'attachment_issues', 'low_self_esteem'],
                'wordings'         => [
                    [8,  12, 'Do you ever feel like no one cares about you?'],
                    [13, 17, 'How often do you feel like nobody really cares about you?'],
                ],
            ],
            [
                'text'             => 'I have been able to deal with problems well.',
                'response_type'    => 'likert_5',
                'source_framework' => 'WEMWBS',
                'min_value'        => 0,
                'max_value'        => 4,
                'is_positive'      => true,
                'risk_weight'      => 0.5,
                'risk_level'       => 'low',
                'age_band_min'     => 10,
                'age_band_max'     => 17,
                'tags'             => ['resilience', 'emotional_regulation'],
                'wordings'         => [
                    [10, 13, 'When things go wrong, I can usually handle it.'],
                    [14, 17, 'I have been able to deal with problems and difficulties well.'],
                ],
            ],
            [
                'text'             => 'How often do you feel like you would rather not be here?',
                'response_type'    => 'likert_5',
                'source_framework' => 'M&MF',
                'min_value'        => 0,
                'max_value'        => 4,
                'is_positive'      => false,
                'risk_weight'      => 4.0,
                'risk_level'       => 'critical',
                'age_band_min'     => 12,
                'age_band_max'     => 17,
                'tags'             => ['suicidal_ideation', 'depression_signs'],
                'wordings'         => [
                    [12, 14, 'Do you ever have thoughts about not wanting to be alive?'],
                    [15, 17, 'How often do you have thoughts about not wanting to be here anymore?'],
                ],
            ],
        ];

        $this->insertQuestions($domainId, $questions);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // BEHAVIOURAL DOMAIN
    // Frameworks: SDQ conduct/hyperactivity subscales, HBSC risk behaviours
    // Constructs: conduct, attention, self-control, risk behaviour, rule-following
    // ─────────────────────────────────────────────────────────────────────────
    private function seedBehaviouralDomain(): void
    {
        $domainId = $this->domainId('Behavioural');

        $questions = [
            [
                'text'             => 'How often do you get into trouble for your behaviour?',
                'response_type'    => 'likert_5',
                'source_framework' => 'SDQ',
                'min_value'        => 0,
                'max_value'        => 4,
                'is_positive'      => false,
                'risk_weight'      => 1.0,
                'risk_level'       => 'medium',
                'age_band_min'     => 8,
                'age_band_max'     => 17,
                'tags'             => ['conduct_issues'],
                'wordings'         => [
                    [8,  12, 'How often do you get into trouble for how you act?'],
                    [13, 17, 'How often do you get into trouble for your behaviour at home or school?'],
                ],
            ],
            [
                'text'             => 'How easy do you find it to concentrate on things?',
                'response_type'    => 'likert_5',
                'source_framework' => 'SDQ',
                'min_value'        => 0,
                'max_value'        => 4,
                'is_positive'      => true,
                'risk_weight'      => 1.0,
                'risk_level'       => 'medium',
                'age_band_min'     => 8,
                'age_band_max'     => 17,
                'tags'             => ['concentration'],
                'wordings'         => [
                    [8,  12, 'Is it easy for you to concentrate and pay attention?'],
                    [13, 17, 'How easy do you find it to concentrate and stay focused?'],
                ],
            ],
            [
                'text'             => 'How often do you stop and think before doing something?',
                'response_type'    => 'likert_5',
                'source_framework' => 'SDQ',
                'min_value'        => 0,
                'max_value'        => 4,
                'is_positive'      => true,
                'risk_weight'      => 0.5,
                'risk_level'       => 'low',
                'age_band_min'     => 8,
                'age_band_max'     => 17,
                'tags'             => ['emotional_regulation', 'conduct_issues'],
                'wordings'         => [
                    [8,  12, 'Do you think before you do things, or do you just do them?'],
                    [13, 17, 'How often do you stop and think before acting?'],
                ],
            ],
            [
                'text'             => 'How often do you feel restless or unable to sit still?',
                'response_type'    => 'likert_5',
                'source_framework' => 'SDQ',
                'min_value'        => 0,
                'max_value'        => 4,
                'is_positive'      => false,
                'risk_weight'      => 1.0,
                'risk_level'       => 'medium',
                'age_band_min'     => 8,
                'age_band_max'     => 17,
                'tags'             => ['concentration', 'anxiety'],
                'wordings'         => [
                    [8,  12, 'Do you often feel like you can\'t sit still or keep calm?'],
                    [13, 17, 'How often do you feel restless or fidgety?'],
                ],
            ],
            [
                'text'             => 'Have you done anything lately that you knew you shouldn\'t?',
                'response_type'    => 'likert_3',
                'source_framework' => 'HBSC',
                'min_value'        => 0,
                'max_value'        => 2,
                'is_positive'      => false,
                'risk_weight'      => 1.0,
                'risk_level'       => 'medium',
                'age_band_min'     => 10,
                'age_band_max'     => 17,
                'tags'             => ['conduct_issues'],
                'wordings'         => [
                    [10, 13, 'Have you done things recently that you knew were wrong?'],
                    [14, 17, 'Have you engaged in any behaviour lately that you knew was not allowed or harmful?'],
                ],
            ],
            [
                'text'             => 'How often do you hurt others — physically or with words?',
                'response_type'    => 'likert_5',
                'source_framework' => 'SDQ',
                'min_value'        => 0,
                'max_value'        => 4,
                'is_positive'      => false,
                'risk_weight'      => 2.0,
                'risk_level'       => 'high',
                'age_band_min'     => 8,
                'age_band_max'     => 17,
                'tags'             => ['conduct_issues', 'peer_conflict'],
                'wordings'         => [
                    [8,  12, 'Do you ever hit people or say mean things to hurt them?'],
                    [13, 17, 'How often do you physically hurt others or say things to hurt people?'],
                ],
            ],
            [
                'text'             => 'Have you used alcohol, cigarettes, or drugs recently?',
                'response_type'    => 'likert_3',
                'source_framework' => 'HBSC',
                'min_value'        => 0,
                'max_value'        => 2,
                'is_positive'      => false,
                'risk_weight'      => 2.0,
                'risk_level'       => 'high',
                'age_band_min'     => 12,
                'age_band_max'     => 17,
                'tags'             => ['substance_use', 'exploitation_risk'],
                'wordings'         => [
                    [12, 14, 'Have you had any alcohol, cigarettes, or drugs lately?'],
                    [15, 17, 'Have you used alcohol, tobacco, or any drugs in the past few weeks?'],
                ],
            ],
            [
                'text'             => 'How well do you follow rules at home and at school?',
                'response_type'    => 'likert_5',
                'source_framework' => 'SDQ',
                'min_value'        => 0,
                'max_value'        => 4,
                'is_positive'      => true,
                'risk_weight'      => 0.5,
                'risk_level'       => 'low',
                'age_band_min'     => 8,
                'age_band_max'     => 17,
                'tags'             => ['conduct_issues'],
                'wordings'         => [
                    [8,  12, 'Do you usually follow the rules at home and school?'],
                    [13, 17, 'How well do you think you follow the rules and expectations at home and school?'],
                ],
            ],
        ];

        $this->insertQuestions($domainId, $questions);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // SOCIAL DOMAIN
    // Frameworks: SDQ peer subscale, HBSC peer/family, CORS relationship scale
    // Constructs: peer relationships, loneliness, family, belonging, support
    // ─────────────────────────────────────────────────────────────────────────
    private function seedSocialDomain(): void
    {
        $domainId = $this->domainId('Social');

        $questions = [
            [
                'text'             => 'How well do you get on with other people your age?',
                'response_type'    => 'emoji_scale',
                'source_framework' => 'CORS',
                'min_value'        => 0,
                'max_value'        => 4,
                'is_positive'      => true,
                'risk_weight'      => 1.0,
                'risk_level'       => 'medium',
                'age_band_min'     => 8,
                'age_band_max'     => 17,
                'tags'             => ['social_isolation', 'positive_relationships'],
                'wordings'         => [
                    [8,  12, 'How well do you get on with other kids your age?'],
                    [13, 17, 'How well do you get on with other people your age?'],
                ],
            ],
            [
                'text'             => 'How often do you feel lonely?',
                'response_type'    => 'likert_5',
                'source_framework' => 'HBSC',
                'min_value'        => 0,
                'max_value'        => 4,
                'is_positive'      => false,
                'risk_weight'      => 2.0,
                'risk_level'       => 'high',
                'age_band_min'     => 8,
                'age_band_max'     => 17,
                'tags'             => ['social_isolation', 'depression_signs'],
                'wordings'         => [
                    [8,  12, 'How often do you feel lonely or left out?'],
                    [13, 17, 'How often do you feel lonely?'],
                ],
            ],
            [
                'text'             => 'Do you have at least one good friend you trust?',
                'response_type'    => 'likert_3',
                'source_framework' => 'HBSC',
                'min_value'        => 0,
                'max_value'        => 2,
                'is_positive'      => true,
                'risk_weight'      => 1.0,
                'risk_level'       => 'medium',
                'age_band_min'     => 8,
                'age_band_max'     => 17,
                'tags'             => ['social_isolation', 'positive_relationships'],
                'wordings'         => [
                    [8,  12, 'Do you have a good friend you can trust?'],
                    [13, 17, 'Do you have at least one close friend you can trust?'],
                ],
            ],
            [
                'text'             => 'How often are you picked on or bullied by others?',
                'response_type'    => 'likert_5',
                'source_framework' => 'SDQ',
                'min_value'        => 0,
                'max_value'        => 4,
                'is_positive'      => false,
                'risk_weight'      => 2.0,
                'risk_level'       => 'high',
                'age_band_min'     => 8,
                'age_band_max'     => 17,
                'tags'             => ['peer_conflict', 'anxiety', 'low_self_esteem'],
                'wordings'         => [
                    [8,  12, 'Do other kids pick on you or bully you?'],
                    [13, 17, 'How often are you picked on or bullied by other people?'],
                ],
            ],
            [
                'text'             => 'How well do you get on with your carer or the people you live with?',
                'response_type'    => 'likert_5',
                'source_framework' => 'CORS',
                'min_value'        => 0,
                'max_value'        => 4,
                'is_positive'      => true,
                'risk_weight'      => 1.0,
                'risk_level'       => 'medium',
                'age_band_min'     => 8,
                'age_band_max'     => 17,
                'tags'             => ['family_conflict', 'attachment_issues', 'placement_anxiety'],
                'wordings'         => [
                    [8,  12, 'How well do you get on with the people you live with?'],
                    [13, 17, 'How well do you get on with your carer or the adults you live with?'],
                ],
            ],
            [
                'text'             => 'Do you feel like you belong where you are living?',
                'response_type'    => 'likert_5',
                'source_framework' => 'OECD',
                'min_value'        => 0,
                'max_value'        => 4,
                'is_positive'      => true,
                'risk_weight'      => 1.0,
                'risk_level'       => 'medium',
                'age_band_min'     => 8,
                'age_band_max'     => 17,
                'tags'             => ['belonging', 'placement_anxiety', 'identity'],
                'wordings'         => [
                    [8,  12, 'Do you feel like you belong in your home?'],
                    [13, 17, 'Do you feel like you belong where you are living?'],
                ],
            ],
            [
                'text'             => 'Is there an adult in your life you can talk to when things are hard?',
                'response_type'    => 'likert_3',
                'source_framework' => 'HBSC',
                'min_value'        => 0,
                'max_value'        => 2,
                'is_positive'      => true,
                'risk_weight'      => 2.0,
                'risk_level'       => 'high',
                'age_band_min'     => 8,
                'age_band_max'     => 17,
                'tags'             => ['social_isolation', 'attachment_issues'],
                'wordings'         => [
                    [8,  12, 'Is there a grown-up you can talk to when you\'re upset?'],
                    [13, 17, 'Is there a trusted adult you can talk to when things are difficult?'],
                ],
            ],
            [
                'text'             => 'How often do you and your friends fall out or argue?',
                'response_type'    => 'likert_5',
                'source_framework' => 'SDQ',
                'min_value'        => 0,
                'max_value'        => 4,
                'is_positive'      => false,
                'risk_weight'      => 1.0,
                'risk_level'       => 'medium',
                'age_band_min'     => 8,
                'age_band_max'     => 17,
                'tags'             => ['peer_conflict'],
                'wordings'         => [
                    [8,  12, 'Do you and your friends argue or fall out a lot?'],
                    [13, 17, 'How often do you have arguments or fall-outs with your friends?'],
                ],
            ],
        ];

        $this->insertQuestions($domainId, $questions);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PHYSICAL DOMAIN
    // Frameworks: HBSC health complaints, health behaviours, OECD health
    // Constructs: sleep, exercise, nutrition, physical health, somatic complaints
    // ─────────────────────────────────────────────────────────────────────────
    private function seedPhysicalDomain(): void
    {
        $domainId = $this->domainId('Physical');

        $questions = [
            [
                'text'             => 'How well have you been sleeping?',
                'response_type'    => 'emoji_scale',
                'source_framework' => 'HBSC',
                'min_value'        => 0,
                'max_value'        => 4,
                'is_positive'      => true,
                'risk_weight'      => 1.0,
                'risk_level'       => 'medium',
                'age_band_min'     => 8,
                'age_band_max'     => 17,
                'tags'             => ['sleep_disruption', 'physical_complaints'],
                'wordings'         => [
                    [8,  12, 'How well have you been sleeping at night?'],
                    [13, 17, 'How well have you been sleeping recently?'],
                ],
            ],
            [
                'text'             => 'How often do you feel tired during the day?',
                'response_type'    => 'likert_5',
                'source_framework' => 'HBSC',
                'min_value'        => 0,
                'max_value'        => 4,
                'is_positive'      => false,
                'risk_weight'      => 1.0,
                'risk_level'       => 'medium',
                'age_band_min'     => 8,
                'age_band_max'     => 17,
                'tags'             => ['sleep_disruption', 'physical_complaints'],
                'wordings'         => [
                    [8,  12, 'How often do you feel really tired during the day?'],
                    [13, 17, 'How often do you feel tired or exhausted during the day?'],
                ],
            ],
            [
                'text'             => 'How often do you eat regular meals each day?',
                'response_type'    => 'likert_5',
                'source_framework' => 'HBSC',
                'min_value'        => 0,
                'max_value'        => 4,
                'is_positive'      => true,
                'risk_weight'      => 1.0,
                'risk_level'       => 'medium',
                'age_band_min'     => 8,
                'age_band_max'     => 17,
                'tags'             => ['poor_nutrition'],
                'wordings'         => [
                    [8,  12, 'Do you eat proper meals every day?'],
                    [13, 17, 'How often do you manage to eat regular meals throughout the day?'],
                ],
            ],
            [
                'text'             => 'How often do you do something active — like sport, walking, or playing outside?',
                'response_type'    => 'likert_5',
                'source_framework' => 'HBSC',
                'min_value'        => 0,
                'max_value'        => 4,
                'is_positive'      => true,
                'risk_weight'      => 0.5,
                'risk_level'       => 'low',
                'age_band_min'     => 8,
                'age_band_max'     => 17,
                'tags'             => ['physical_inactivity'],
                'wordings'         => [
                    [8,  12, 'How often do you play outside or do something active?'],
                    [13, 17, 'How often do you do physical activity like sport, exercise, or walking?'],
                ],
            ],
            [
                'text'             => 'How often do you have headaches, stomach aches, or feel unwell?',
                'response_type'    => 'likert_5',
                'source_framework' => 'HBSC',
                'min_value'        => 0,
                'max_value'        => 4,
                'is_positive'      => false,
                'risk_weight'      => 1.0,
                'risk_level'       => 'medium',
                'age_band_min'     => 8,
                'age_band_max'     => 17,
                'tags'             => ['physical_complaints', 'anxiety'],
                'wordings'         => [
                    [8,  12, 'Do you often get headaches or tummy aches?'],
                    [13, 17, 'How often do you experience headaches, stomach aches, or other physical symptoms?'],
                ],
            ],
            [
                'text'             => 'How do you feel about what you eat?',
                'response_type'    => 'likert_5',
                'source_framework' => 'HBSC',
                'min_value'        => 0,
                'max_value'        => 4,
                'is_positive'      => true,
                'risk_weight'      => 1.0,
                'risk_level'       => 'medium',
                'age_band_min'     => 10,
                'age_band_max'     => 17,
                'tags'             => ['poor_nutrition', 'disordered_eating'],
                'wordings'         => [
                    [10, 13, 'Do you feel OK about the food you eat?'],
                    [14, 17, 'How do you feel about your relationship with food and eating?'],
                ],
            ],
            [
                'text'             => 'How would you describe your physical health overall?',
                'response_type'    => 'slider',
                'source_framework' => 'OECD',
                'min_value'        => 0,
                'max_value'        => 10,
                'is_positive'      => true,
                'risk_weight'      => 0.5,
                'risk_level'       => 'low',
                'age_band_min'     => 10,
                'age_band_max'     => 17,
                'tags'             => ['physical_complaints'],
                'wordings'         => [
                    [10, 13, 'How healthy do you feel in your body overall?'],
                    [14, 17, 'How would you rate your physical health overall?'],
                ],
            ],
        ];

        $this->insertQuestions($domainId, $questions);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // EDUCATION DOMAIN
    // Frameworks: HBSC school module, CORS school scale, OECD education
    // Constructs: school liking, attendance, academic confidence, teacher relations
    // ─────────────────────────────────────────────────────────────────────────
    private function seedEducationDomain(): void
    {
        $domainId = $this->domainId('Education');

        $questions = [
            [
                'text'             => 'How much do you like school?',
                'response_type'    => 'emoji_scale',
                'source_framework' => 'HBSC',
                'min_value'        => 0,
                'max_value'        => 4,
                'is_positive'      => true,
                'risk_weight'      => 1.0,
                'risk_level'       => 'medium',
                'age_band_min'     => 8,
                'age_band_max'     => 17,
                'tags'             => ['school_disengagement'],
                'wordings'         => [
                    [8,  12, 'Do you like going to school?'],
                    [13, 17, 'How much do you enjoy school?'],
                ],
            ],
            [
                'text'             => 'How often have you missed school recently?',
                'response_type'    => 'likert_5',
                'source_framework' => 'OECD',
                'min_value'        => 0,
                'max_value'        => 4,
                'is_positive'      => false,
                'risk_weight'      => 2.0,
                'risk_level'       => 'high',
                'age_band_min'     => 8,
                'age_band_max'     => 17,
                'tags'             => ['attendance_concern', 'school_disengagement'],
                'wordings'         => [
                    [8,  12, 'How many days have you missed school lately?'],
                    [13, 17, 'How often have you been absent from school recently?'],
                ],
            ],
            [
                'text'             => 'How confident do you feel about your schoolwork?',
                'response_type'    => 'likert_5',
                'source_framework' => 'CORS',
                'min_value'        => 0,
                'max_value'        => 4,
                'is_positive'      => true,
                'risk_weight'      => 1.0,
                'risk_level'       => 'medium',
                'age_band_min'     => 8,
                'age_band_max'     => 17,
                'tags'             => ['school_disengagement', 'low_self_esteem'],
                'wordings'         => [
                    [8,  12, 'Do you feel like you can do your school work?'],
                    [13, 17, 'How confident do you feel about managing your schoolwork?'],
                ],
            ],
            [
                'text'             => 'How well do you get on with your teachers?',
                'response_type'    => 'likert_5',
                'source_framework' => 'HBSC',
                'min_value'        => 0,
                'max_value'        => 4,
                'is_positive'      => true,
                'risk_weight'      => 1.0,
                'risk_level'       => 'medium',
                'age_band_min'     => 8,
                'age_band_max'     => 17,
                'tags'             => ['school_disengagement', 'positive_relationships'],
                'wordings'         => [
                    [8,  12, 'Do you get on well with your teachers?'],
                    [13, 17, 'How well do you get on with your teachers?'],
                ],
            ],
            [
                'text'             => 'How often do you feel worried or stressed about school?',
                'response_type'    => 'likert_5',
                'source_framework' => 'HBSC',
                'min_value'        => 0,
                'max_value'        => 4,
                'is_positive'      => false,
                'risk_weight'      => 1.0,
                'risk_level'       => 'medium',
                'age_band_min'     => 8,
                'age_band_max'     => 17,
                'tags'             => ['anxiety', 'school_disengagement'],
                'wordings'         => [
                    [8,  12, 'Does school make you feel worried or stressed?'],
                    [13, 17, 'How often do you feel anxious or stressed about school?'],
                ],
            ],
            [
                'text'             => 'Do you feel like your teachers believe you can do well?',
                'response_type'    => 'likert_3',
                'source_framework' => 'HBSC',
                'min_value'        => 0,
                'max_value'        => 2,
                'is_positive'      => true,
                'risk_weight'      => 0.5,
                'risk_level'       => 'low',
                'age_band_min'     => 8,
                'age_band_max'     => 17,
                'tags'             => ['school_disengagement', 'low_self_esteem'],
                'wordings'         => [
                    [8,  12, 'Do you think your teachers believe in you?'],
                    [13, 17, 'Do you feel that your teachers believe you are capable of doing well?'],
                ],
            ],
        ];

        $this->insertQuestions($domainId, $questions);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // SAFETY DOMAIN
    // Frameworks: LAC framework, HM Government (2018), OECD (population-level
    //             safety constructs extended for looked-after children)
    // Constructs: personal safety, harm exposure, feeling safe, exploitation
    // Note: All safety questions carry high or critical risk weights.
    //       Safeguarding-tagged questions bypass the scoring pipeline entirely
    //       and generate immediate alerts.
    // ─────────────────────────────────────────────────────────────────────────
    private function seedSafetyDomain(): void
    {
        $domainId = $this->domainId('Safety');

        $questions = [
            [
                'text'             => 'How safe do you feel where you are living?',
                'response_type'    => 'emoji_scale',
                'source_framework' => 'OECD',
                'min_value'        => 0,
                'max_value'        => 4,
                'is_positive'      => true,
                'risk_weight'      => 4.0,
                'risk_level'       => 'critical',
                'age_band_min'     => 8,
                'age_band_max'     => 17,
                'tags'             => ['placement_anxiety', 'neglect'],
                'wordings'         => [
                    [8,  12, 'Do you feel safe where you live?'],
                    [13, 17, 'How safe do you feel in your current home?'],
                ],
            ],
            [
                'text'             => 'Has anyone hurt you or made you feel unsafe recently?',
                'response_type'    => 'likert_3',
                'source_framework' => 'OECD',
                'min_value'        => 0,
                'max_value'        => 2,
                'is_positive'      => false,
                'risk_weight'      => 4.0,
                'risk_level'       => 'critical',
                'age_band_min'     => 8,
                'age_band_max'     => 17,
                'tags'             => ['abuse_physical', 'abuse_emotional', 'neglect'],
                'wordings'         => [
                    [8,  12, 'Has anyone hurt you or made you feel scared recently?'],
                    [13, 17, 'Has anyone hurt you or made you feel unsafe recently?'],
                ],
            ],
            [
                'text'             => 'Do you feel like you can speak up if something is wrong?',
                'response_type'    => 'likert_5',
                'source_framework' => 'OECD',
                'min_value'        => 0,
                'max_value'        => 4,
                'is_positive'      => true,
                'risk_weight'      => 2.0,
                'risk_level'       => 'high',
                'age_band_min'     => 8,
                'age_band_max'     => 17,
                'tags'             => ['social_isolation', 'neglect'],
                'wordings'         => [
                    [8,  12, 'Do you feel like you can tell someone if something is wrong?'],
                    [13, 17, 'Do you feel you can speak up or tell someone if something is wrong in your life?'],
                ],
            ],
            [
                'text'             => 'Has anyone asked you to keep a secret that made you uncomfortable?',
                'response_type'    => 'likert_3',
                'source_framework' => 'OECD',
                'min_value'        => 0,
                'max_value'        => 2,
                'is_positive'      => false,
                'risk_weight'      => 4.0,
                'risk_level'       => 'critical',
                'age_band_min'     => 8,
                'age_band_max'     => 17,
                'tags'             => ['abuse_emotional', 'abuse_sexual', 'exploitation_risk'],
                'wordings'         => [
                    [8,  12, 'Has anyone asked you to keep a secret that felt wrong?'],
                    [13, 17, 'Has anyone asked you to keep a secret that made you feel uncomfortable or uneasy?'],
                ],
            ],
            [
                'text'             => 'Have you ever hurt yourself on purpose?',
                'response_type'    => 'likert_3',
                'source_framework' => 'OECD',
                'min_value'        => 0,
                'max_value'        => 2,
                'is_positive'      => false,
                'risk_weight'      => 4.0,
                'risk_level'       => 'critical',
                'age_band_min'     => 11,
                'age_band_max'     => 17,
                'tags'             => ['self_harm', 'depression_signs'],
                'wordings'         => [
                    [11, 13, 'Have you ever hurt yourself on purpose?'],
                    [14, 17, 'Have you ever deliberately hurt yourself?'],
                ],
            ],
            [
                'text'             => 'Do you feel safe when you are at school?',
                'response_type'    => 'likert_5',
                'source_framework' => 'OECD',
                'min_value'        => 0,
                'max_value'        => 4,
                'is_positive'      => true,
                'risk_weight'      => 2.0,
                'risk_level'       => 'high',
                'age_band_min'     => 8,
                'age_band_max'     => 17,
                'tags'             => ['peer_conflict', 'anxiety'],
                'wordings'         => [
                    [8,  12, 'Do you feel safe at school?'],
                    [13, 17, 'How safe do you feel when you are at school?'],
                ],
            ],
            [
                'text'             => 'Has anyone online made you feel uncomfortable or unsafe?',
                'response_type'    => 'likert_3',
                'source_framework' => 'HBSC',
                'min_value'        => 0,
                'max_value'        => 2,
                'is_positive'      => false,
                'risk_weight'      => 4.0,
                'risk_level'       => 'critical',
                'age_band_min'     => 10,
                'age_band_max'     => 17,
                'tags'             => ['exploitation_risk', 'abuse_emotional'],
                'wordings'         => [
                    [10, 13, 'Has anyone online said or done things that made you feel uncomfortable?'],
                    [14, 17, 'Has anyone online made you feel unsafe, uncomfortable, or pressured?'],
                ],
            ],
        ];

        $this->insertQuestions($domainId, $questions);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────────────────

    private function domainId(string $name): int
    {
        return DB::table('domains')->where('name', $name)->value('id');
    }

    private function tagId(string $name): ?int
    {
        return DB::table('tags')->where('name', $name)->value('id');
    }

    private function insertQuestions(int $domainId, array $questions): void
    {
        foreach ($questions as $q) {
            // Upsert on text to allow re-seeding safely
            $existing = DB::table('questions')->where('text', $q['text'])->first();

            $questionData = [
                'domain_id'        => $domainId,
                'text'             => $q['text'],
                'response_type'    => $q['response_type'],
                'source_framework' => $q['source_framework'],
                'min_value'        => $q['min_value'],
                'max_value'        => $q['max_value'],
                'is_positive'      => $q['is_positive'],
                'risk_weight'      => $q['risk_weight'],
                'risk_level'       => $q['risk_level'],
                'age_band_min'     => $q['age_band_min'],
                'age_band_max'     => $q['age_band_max'],
                'is_active'        => true,
                'version'          => 1,
                'created_at'       => now(),
                'updated_at'       => now(),
            ];

            if ($existing) {
                DB::table('questions')->where('id', $existing->id)->update($questionData);
                $questionId = $existing->id;
            } else {
                $questionId = DB::table('questions')->insertGetId($questionData);
            }

            // Seed age-banded wordings
            foreach ($q['wordings'] as [$ageMin, $ageMax, $text]) {
                DB::table('question_wordings')->updateOrInsert(
                    ['question_id' => $questionId, 'age_min' => $ageMin, 'age_max' => $ageMax],
                    ['text' => $text, 'created_at' => now(), 'updated_at' => now()]
                );
            }

            // Seed tag associations
            foreach ($q['tags'] as $tagName) {
                $tagId = $this->tagId($tagName);
                if (!$tagId) continue;

                DB::table('question_tag')->updateOrInsert(
                    ['question_id' => $questionId, 'tag_id' => $tagId]
                );
            }
        }
    }
}
