<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class QuestionSeeder extends Seeder
{
    public function run(): void
    {

    DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    DB::table('question_tag')->truncate();
    DB::table('question_wordings')->truncate();
    DB::table('questions')->truncate();
    DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $questions = array_merge(
            $this->emotionalQuestions(),
            $this->behaviouralQuestions(),
            $this->socialQuestions(),
            $this->physicalQuestions(),
            $this->educationQuestions(),
            $this->safetyQuestions(),
            $this->lifeSatisfactionQuestions()
        );

        foreach ($questions as $q) {
            DB::table('questions')->insert(array_merge($q, [
                'is_active'  => 1,
                'version'    => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    // ── Domain 1: Emotional ───────────────────────────────────────────────────

    private function emotionalQuestions(): array
    {
        return [
            // Q1
            [
                'domain_id'        => 1,
                'text'             => 'Over the last two weeks, how often have you felt cheerful and in good spirits?',
                'response_type'    => 'likert_5',
                'source_framework' => 'WHO5',
                'min_value'        => 0,
                'max_value'        => 4,
                'is_positive'      => 1,
                'risk_weight'      => 1.5,
                'risk_level'       => 'medium',
                'age_band_min'     => 11,
                'age_band_max'     => 18,
                'option_labels'    => json_encode(['At no time', 'Some of the time', 'Less than half the time', 'Most of the time', 'All the time']),
            ],
            // Q2
            [
                'domain_id'        => 1,
                'text'             => 'Over the last two weeks, how often have you felt calm and relaxed?',
                'response_type'    => 'likert_5',
                'source_framework' => 'WHO5',
                'min_value'        => 0,
                'max_value'        => 4,
                'is_positive'      => 1,
                'risk_weight'      => 1.5,
                'risk_level'       => 'medium',
                'age_band_min'     => 11,
                'age_band_max'     => 18,
                'option_labels'    => json_encode(['At no time', 'Some of the time', 'Less than half the time', 'Most of the time', 'All the time']),
            ],
            // Q3
            [
                'domain_id'        => 1,
                'text'             => 'Over the last two weeks, how often have you woken up feeling fresh and rested?',
                'response_type'    => 'likert_5',
                'source_framework' => 'WHO5',
                'min_value'        => 0,
                'max_value'        => 4,
                'is_positive'      => 1,
                'risk_weight'      => 1.5,
                'risk_level'       => 'medium',
                'age_band_min'     => 11,
                'age_band_max'     => 18,
                'option_labels'    => json_encode(['At no time', 'Some of the time', 'Less than half the time', 'Most of the time', 'All the time']),
            ],
            // Q4
            [
                'domain_id'        => 1,
                'text'             => 'In the last 6 months, how often have you felt low or down?',
                'response_type'    => 'likert_5',
                'source_framework' => 'HBSC',
                'min_value'        => 1,
                'max_value'        => 5,
                'is_positive'      => 0,
                'risk_weight'      => 2.5,
                'risk_level'       => 'high',
                'age_band_min'     => 11,
                'age_band_max'     => 18,
                'option_labels'    => json_encode(['Rarely or never', 'About every month', 'About every week', 'More than once a week', 'About every day']),
            ],
            // Q5
            [
                'domain_id'        => 1,
                'text'             => 'In the last 6 months, how often have you felt nervous or anxious?',
                'response_type'    => 'likert_5',
                'source_framework' => 'HBSC',
                'min_value'        => 1,
                'max_value'        => 5,
                'is_positive'      => 0,
                'risk_weight'      => 2.5,
                'risk_level'       => 'high',
                'age_band_min'     => 11,
                'age_band_max'     => 18,
                'option_labels'    => json_encode(['Rarely or never', 'About every month', 'About every week', 'More than once a week', 'About every day']),
            ],
            // Q6
            [
                'domain_id'        => 1,
                'text'             => 'During the past 12 months, how often have you felt lonely?',
                'response_type'    => 'likert_5',
                'source_framework' => 'HBSC',
                'min_value'        => 1,
                'max_value'        => 5,
                'is_positive'      => 0,
                'risk_weight'      => 3.0,
                'risk_level'       => 'high',
                'age_band_min'     => 11,
                'age_band_max'     => 18,
                'option_labels'    => json_encode(['Never', 'Rarely', 'Sometimes', 'Most of the time', 'Always']),
            ],
            // Q7
            [
                'domain_id'        => 1,
                'text'             => 'In the last month, how often have you felt that difficulties were piling up so high you could not overcome them?',
                'response_type'    => 'likert_5',
                'source_framework' => 'PSS4',
                'min_value'        => 1,
                'max_value'        => 5,
                'is_positive'      => 0,
                'risk_weight'      => 2.5,
                'risk_level'       => 'high',
                'age_band_min'     => 13,
                'age_band_max'     => 18,
                'option_labels'    => json_encode(['Never', 'Almost never', 'Sometimes', 'Fairly often', 'Very often']),
            ],
            // Q8
            [
                'domain_id'        => 1,
                'text'             => 'In the last month, how often have you felt confident about your ability to handle your personal problems?',
                'response_type'    => 'likert_5',
                'source_framework' => 'PSS4',
                'min_value'        => 1,
                'max_value'        => 5,
                'is_positive'      => 1,
                'risk_weight'      => 2.0,
                'risk_level'       => 'medium',
                'age_band_min'     => 13,
                'age_band_max'     => 18,
                'option_labels'    => json_encode(['Never', 'Almost never', 'Sometimes', 'Fairly often', 'Very often']),
            ],
            // Q9
            [
                'domain_id'        => 1,
                'text'             => 'My daily life has been filled with things that interest me',
                'response_type'    => 'likert_5',
                'source_framework' => 'WHO5',
                'min_value'        => 0,
                'max_value'        => 4,
                'is_positive'      => 1,
                'risk_weight'      => 1.5,
                'risk_level'       => 'medium',
                'age_band_min'     => 11,
                'age_band_max'     => 18,
                'option_labels'    => json_encode(['At no time', 'Some of the time', 'Less than half the time', 'Most of the time', 'All the time']),
            ],
            // Q10
            [
                'domain_id'        => 1,
                'text'             => 'In the last 6 months, how often have you had difficulty sleeping?',
                'response_type'    => 'likert_5',
                'source_framework' => 'HBSC',
                'min_value'        => 1,
                'max_value'        => 5,
                'is_positive'      => 0,
                'risk_weight'      => 2.0,
                'risk_level'       => 'medium',
                'age_band_min'     => 11,
                'age_band_max'     => 18,
                'option_labels'    => json_encode(['Rarely or never', 'About every month', 'About every week', 'More than once a week', 'About every day']),
            ],
        ];
    }

    // ── Domain 2: Behavioural ─────────────────────────────────────────────────

    private function behaviouralQuestions(): array
    {
        return [
            // Q11
            [
                'domain_id'        => 2,
                'text'             => 'In the last 6 months, how often have you felt irritable or had a bad temper?',
                'response_type'    => 'likert_5',
                'source_framework' => 'HBSC',
                'min_value'        => 1,
                'max_value'        => 5,
                'is_positive'      => 0,
                'risk_weight'      => 2.0,
                'risk_level'       => 'medium',
                'age_band_min'     => 11,
                'age_band_max'     => 18,
                'option_labels'    => json_encode(['Rarely or never', 'About every month', 'About every week', 'More than once a week', 'About every day']),
            ],
            // Q12
            [
                'domain_id'        => 2,
                'text'             => 'How often do you find it hard to concentrate on something?',
                'response_type'    => 'likert_5',
                'source_framework' => 'SDQ',
                'min_value'        => 1,
                'max_value'        => 5,
                'is_positive'      => 0,
                'risk_weight'      => 2.0,
                'risk_level'       => 'medium',
                'age_band_min'     => 11,
                'age_band_max'     => 18,
                'option_labels'    => json_encode(['Never', 'Rarely', 'Sometimes', 'Often', 'Always']),
            ],
            // Q13
            [
                'domain_id'        => 2,
                'text'             => 'How often do you manage to do the things you decide to do?',
                'response_type'    => 'likert_5',
                'source_framework' => 'SE',
                'min_value'        => 1,
                'max_value'        => 5,
                'is_positive'      => 1,
                'risk_weight'      => 1.5,
                'risk_level'       => 'low',
                'age_band_min'     => 11,
                'age_band_max'     => 18,
                'option_labels'    => json_encode(['Never', 'Rarely', 'Sometimes', 'Most of the time', 'Always']),
            ],
            // Q14
            [
                'domain_id'        => 2,
                'text'             => 'How often do you find a solution to a problem if you try hard enough?',
                'response_type'    => 'likert_5',
                'source_framework' => 'SE',
                'min_value'        => 1,
                'max_value'        => 5,
                'is_positive'      => 1,
                'risk_weight'      => 1.5,
                'risk_level'       => 'low',
                'age_band_min'     => 11,
                'age_band_max'     => 18,
                'option_labels'    => json_encode(['Never', 'Rarely', 'Sometimes', 'Most of the time', 'Always']),
            ],
            // Q15
            [
                'domain_id'        => 2,
                'text'             => 'On how many days in the past month have you drunk alcohol?',
                'response_type'    => 'likert_5',
                'source_framework' => 'HBSC',
                'min_value'        => 1,
                'max_value'        => 5,
                'is_positive'      => 0,
                'risk_weight'      => 3.0,
                'risk_level'       => 'high',
                'age_band_min'     => 13,
                'age_band_max'     => 18,
                'option_labels'    => json_encode(['Never', '1–2 days', '3–5 days', '6–9 days', '10 days or more']),
            ],
            // Q16
            [
                'domain_id'        => 2,
                'text'             => 'On how many days in the past month have you smoked cigarettes?',
                'response_type'    => 'likert_5',
                'source_framework' => 'HBSC',
                'min_value'        => 1,
                'max_value'        => 5,
                'is_positive'      => 0,
                'risk_weight'      => 3.0,
                'risk_level'       => 'high',
                'age_band_min'     => 13,
                'age_band_max'     => 18,
                'option_labels'    => json_encode(['Never', '1–2 days', '3–5 days', '6–9 days', '10 days or more']),
            ],
            // Q17
            [
                'domain_id'        => 2,
                'text'             => 'During the past year, have you regularly found that you could not stop thinking about when you could use social media again?',
                'response_type'    => 'likert_3',
                'source_framework' => 'HBSC',
                'min_value'        => 1,
                'max_value'        => 3,
                'is_positive'      => 0,
                'risk_weight'      => 1.5,
                'risk_level'       => 'medium',
                'age_band_min'     => 11,
                'age_band_max'     => 18,
                'option_labels'    => json_encode(['No', 'Sometimes', 'Yes, regularly']),
            ],
            // Q18
            [
                'domain_id'        => 2,
                'text'             => 'How often do you feel in control of the way you behave?',
                'response_type'    => 'likert_5',
                'source_framework' => 'PSS4',
                'min_value'        => 1,
                'max_value'        => 5,
                'is_positive'      => 1,
                'risk_weight'      => 2.0,
                'risk_level'       => 'medium',
                'age_band_min'     => 11,
                'age_band_max'     => 18,
                'option_labels'    => json_encode(['Never', 'Rarely', 'Sometimes', 'Most of the time', 'Always']),
            ],
        ];
    }

    // ── Domain 3: Social ──────────────────────────────────────────────────────

    private function socialQuestions(): array
    {
        return [
            // Q19
            [
                'domain_id'        => 3,
                'text'             => 'My family really tries to help me',
                'response_type'    => 'likert_5',
                'source_framework' => 'MSPSS',
                'min_value'        => 1,
                'max_value'        => 7,
                'is_positive'      => 1,
                'risk_weight'      => 3.0,
                'risk_level'       => 'high',
                'age_band_min'     => 11,
                'age_band_max'     => 18,
                'option_labels'    => json_encode(['Very strongly disagree', 'Strongly disagree', 'Disagree', 'Neutral', 'Agree', 'Strongly agree', 'Very strongly agree']),
            ],
            // Q20
            [
                'domain_id'        => 3,
                'text'             => 'I can talk about my problems with my family or carers',
                'response_type'    => 'likert_5',
                'source_framework' => 'MSPSS',
                'min_value'        => 1,
                'max_value'        => 7,
                'is_positive'      => 1,
                'risk_weight'      => 3.0,
                'risk_level'       => 'high',
                'age_band_min'     => 11,
                'age_band_max'     => 18,
                'option_labels'    => json_encode(['Very strongly disagree', 'Strongly disagree', 'Disagree', 'Neutral', 'Agree', 'Strongly agree', 'Very strongly agree']),
            ],
            // Q21
            [
                'domain_id'        => 3,
                'text'             => 'My friends really try to help me',
                'response_type'    => 'likert_5',
                'source_framework' => 'MSPSS',
                'min_value'        => 1,
                'max_value'        => 7,
                'is_positive'      => 1,
                'risk_weight'      => 2.0,
                'risk_level'       => 'medium',
                'age_band_min'     => 11,
                'age_band_max'     => 18,
                'option_labels'    => json_encode(['Very strongly disagree', 'Strongly disagree', 'Disagree', 'Neutral', 'Agree', 'Strongly agree', 'Very strongly agree']),
            ],
            // Q22
            [
                'domain_id'        => 3,
                'text'             => 'I can count on my friends when things go wrong',
                'response_type'    => 'likert_5',
                'source_framework' => 'MSPSS',
                'min_value'        => 1,
                'max_value'        => 7,
                'is_positive'      => 1,
                'risk_weight'      => 2.0,
                'risk_level'       => 'medium',
                'age_band_min'     => 11,
                'age_band_max'     => 18,
                'option_labels'    => json_encode(['Very strongly disagree', 'Strongly disagree', 'Disagree', 'Neutral', 'Agree', 'Strongly agree', 'Very strongly agree']),
            ],
            // Q23
            [
                'domain_id'        => 3,
                'text'             => 'Other people my age accept me as I am',
                'response_type'    => 'likert_5',
                'source_framework' => 'HBSC',
                'min_value'        => 1,
                'max_value'        => 5,
                'is_positive'      => 1,
                'risk_weight'      => 2.5,
                'risk_level'       => 'high',
                'age_band_min'     => 11,
                'age_band_max'     => 18,
                'option_labels'    => json_encode(['Strongly disagree', 'Disagree', 'Neither agree nor disagree', 'Agree', 'Strongly agree']),
            ],
            // Q24
            [
                'domain_id'        => 3,
                'text'             => 'How easy is it for you to talk to your carer about things that really bother you?',
                'response_type'    => 'likert_5',
                'source_framework' => 'HBSC',
                'min_value'        => 1,
                'max_value'        => 4,
                'is_positive'      => 1,
                'risk_weight'      => 3.0,
                'risk_level'       => 'high',
                'age_band_min'     => 11,
                'age_band_max'     => 18,
                'option_labels'    => json_encode(['Very difficult', 'Difficult', 'Easy', 'Very easy']),
            ],
            // Q25
            [
                'domain_id'        => 3,
                'text'             => 'How often do you spend time with friends outside of school?',
                'response_type'    => 'likert_5',
                'source_framework' => 'HBSC',
                'min_value'        => 1,
                'max_value'        => 5,
                'is_positive'      => 1,
                'risk_weight'      => 1.5,
                'risk_level'       => 'medium',
                'age_band_min'     => 11,
                'age_band_max'     => 18,
                'option_labels'    => json_encode(['Never', 'Rarely', 'Sometimes', 'Often', 'Every day']),
            ],
            // Q26
            [
                'domain_id'        => 3,
                'text'             => 'Most of the students in my class are kind and helpful',
                'response_type'    => 'likert_5',
                'source_framework' => 'HBSC',
                'min_value'        => 1,
                'max_value'        => 5,
                'is_positive'      => 1,
                'risk_weight'      => 1.5,
                'risk_level'       => 'low',
                'age_band_min'     => 11,
                'age_band_max'     => 18,
                'option_labels'    => json_encode(['Strongly disagree', 'Disagree', 'Neither agree nor disagree', 'Agree', 'Strongly agree']),
            ],
        ];
    }

    // ── Domain 4: Physical ────────────────────────────────────────────────────

    private function physicalQuestions(): array
    {
        return [
            // Q27
            [
                'domain_id'        => 4,
                'text'             => 'Would you say your health is …?',
                'response_type'    => 'likert_5',
                'source_framework' => 'HBSC',
                'min_value'        => 1,
                'max_value'        => 4,
                'is_positive'      => 1,
                'risk_weight'      => 2.0,
                'risk_level'       => 'medium',
                'age_band_min'     => 11,
                'age_band_max'     => 18,
                'option_labels'    => json_encode(['Poor', 'Fair', 'Good', 'Excellent']),
            ],
            // Q28
            [
                'domain_id'        => 4,
                'text'             => 'In the last 6 months, how often have you had headaches?',
                'response_type'    => 'likert_5',
                'source_framework' => 'HBSC',
                'min_value'        => 1,
                'max_value'        => 5,
                'is_positive'      => 0,
                'risk_weight'      => 1.5,
                'risk_level'       => 'medium',
                'age_band_min'     => 11,
                'age_band_max'     => 18,
                'option_labels'    => json_encode(['Rarely or never', 'About every month', 'About every week', 'More than once a week', 'About every day']),
            ],
            // Q29
            [
                'domain_id'        => 4,
                'text'             => 'In the last 6 months, how often have you had stomachaches?',
                'response_type'    => 'likert_5',
                'source_framework' => 'HBSC',
                'min_value'        => 1,
                'max_value'        => 5,
                'is_positive'      => 0,
                'risk_weight'      => 1.5,
                'risk_level'       => 'medium',
                'age_band_min'     => 11,
                'age_band_max'     => 18,
                'option_labels'    => json_encode(['Rarely or never', 'About every month', 'About every week', 'More than once a week', 'About every day']),
            ],
            // Q30
            [
                'domain_id'        => 4,
                'text'             => 'Over the past 7 days, on how many days were you physically active for at least 60 minutes?',
                'response_type'    => 'slider',
                'source_framework' => 'HBSC',
                'min_value'        => 0,
                'max_value'        => 7,
                'is_positive'      => 1,
                'risk_weight'      => 1.5,
                'risk_level'       => 'medium',
                'age_band_min'     => 11,
                'age_band_max'     => 18,
                'option_labels'    => null,
            ],
            // Q31
            [
                'domain_id'        => 4,
                'text'             => 'Outside school hours, how often do you exercise so much that you get out of breath or sweat?',
                'response_type'    => 'likert_5',
                'source_framework' => 'HBSC',
                'min_value'        => 1,
                'max_value'        => 5,
                'is_positive'      => 1,
                'risk_weight'      => 1.5,
                'risk_level'       => 'low',
                'age_band_min'     => 11,
                'age_band_max'     => 18,
                'option_labels'    => json_encode(['Never', 'Less than once a month', 'Once a week or less', '2–3 times a week', 'Every day or almost every day']),
            ],
            // Q32
            [
                'domain_id'        => 4,
                'text'             => 'How often do you usually have breakfast on school days?',
                'response_type'    => 'likert_5',
                'source_framework' => 'HBSC',
                'min_value'        => 1,
                'max_value'        => 5,
                'is_positive'      => 1,
                'risk_weight'      => 1.0,
                'risk_level'       => 'low',
                'age_band_min'     => 11,
                'age_band_max'     => 18,
                'option_labels'    => json_encode(['Never', '1–2 days', '3 days', '4 days', '5 days (every day)']),
            ],
            // Q33
            [
                'domain_id'        => 4,
                'text'             => 'How many times a week do you usually eat fruit?',
                'response_type'    => 'likert_5',
                'source_framework' => 'HBSC',
                'min_value'        => 1,
                'max_value'        => 5,
                'is_positive'      => 1,
                'risk_weight'      => 1.0,
                'risk_level'       => 'low',
                'age_band_min'     => 11,
                'age_band_max'     => 18,
                'option_labels'    => json_encode(['Never', 'Less than once a week', 'Once a week', '2–4 days a week', 'Every day or more']),
            ],
            // Q34
            [
                'domain_id'        => 4,
                'text'             => 'Do you think your body is …?',
                'response_type'    => 'likert_5',
                'source_framework' => 'HBSC',
                'min_value'        => 1,
                'max_value'        => 5,
                'is_positive'      => 0,
                'risk_weight'      => 2.0,
                'risk_level'       => 'medium',
                'age_band_min'     => 11,
                'age_band_max'     => 18,
                'option_labels'    => json_encode(['About the right size', 'A bit too thin', 'Much too thin', 'A bit too fat', 'Much too fat']),
            ],
        ];
    }

    // ── Domain 5: Education ───────────────────────────────────────────────────

    private function educationQuestions(): array
    {
        return [
            // Q35
            [
                'domain_id'        => 5,
                'text'             => 'How do you feel about school at the moment?',
                'response_type'    => 'likert_5',
                'source_framework' => 'HBSC',
                'min_value'        => 1,
                'max_value'        => 4,
                'is_positive'      => 1,
                'risk_weight'      => 2.0,
                'risk_level'       => 'medium',
                'age_band_min'     => 11,
                'age_band_max'     => 18,
                'option_labels'    => json_encode(['I don\'t like it at all', 'I don\'t like it very much', 'I like it a bit', 'I like it a lot']),
            ],
            // Q36
            [
                'domain_id'        => 5,
                'text'             => 'How pressured do you feel by the schoolwork you have to do?',
                'response_type'    => 'likert_5',
                'source_framework' => 'HBSC',
                'min_value'        => 1,
                'max_value'        => 4,
                'is_positive'      => 0,
                'risk_weight'      => 2.0,
                'risk_level'       => 'medium',
                'age_band_min'     => 11,
                'age_band_max'     => 18,
                'option_labels'    => json_encode(['Not at all', 'A little', 'Some', 'A lot']),
            ],
            // Q37
            [
                'domain_id'        => 5,
                'text'             => 'I feel that my teachers care about me as a person',
                'response_type'    => 'likert_5',
                'source_framework' => 'HBSC',
                'min_value'        => 1,
                'max_value'        => 5,
                'is_positive'      => 1,
                'risk_weight'      => 2.0,
                'risk_level'       => 'medium',
                'age_band_min'     => 11,
                'age_band_max'     => 18,
                'option_labels'    => json_encode(['Strongly disagree', 'Disagree', 'Neither agree nor disagree', 'Agree', 'Strongly agree']),
            ],
            // Q38
            [
                'domain_id'        => 5,
                'text'             => 'I feel that my teachers accept me as I am',
                'response_type'    => 'likert_5',
                'source_framework' => 'HBSC',
                'min_value'        => 1,
                'max_value'        => 5,
                'is_positive'      => 1,
                'risk_weight'      => 1.5,
                'risk_level'       => 'low',
                'age_band_min'     => 11,
                'age_band_max'     => 18,
                'option_labels'    => json_encode(['Strongly disagree', 'Disagree', 'Neither agree nor disagree', 'Agree', 'Strongly agree']),
            ],
            // Q39
            [
                'domain_id'        => 5,
                'text'             => 'How often do you attend school?',
                'response_type'    => 'likert_5',
                'source_framework' => 'CORS',
                'min_value'        => 1,
                'max_value'        => 5,
                'is_positive'      => 1,
                'risk_weight'      => 2.5,
                'risk_level'       => 'high',
                'age_band_min'     => 11,
                'age_band_max'     => 18,
                'option_labels'    => json_encode(['Never', 'Rarely', 'Sometimes', 'Most of the time', 'Always']),
            ],
            // Q40
            [
                'domain_id'        => 5,
                'text'             => 'How confident do you feel about your schoolwork?',
                'response_type'    => 'likert_5',
                'source_framework' => 'CORS',
                'min_value'        => 1,
                'max_value'        => 5,
                'is_positive'      => 1,
                'risk_weight'      => 1.5,
                'risk_level'       => 'medium',
                'age_band_min'     => 11,
                'age_band_max'     => 18,
                'option_labels'    => json_encode(['Not at all confident', 'A little confident', 'Somewhat confident', 'Quite confident', 'Very confident']),
            ],
        ];
    }

    // ── Domain 6: Safety ──────────────────────────────────────────────────────

    private function safetyQuestions(): array
    {
        return [
            // Q41
            [
                'domain_id'        => 6,
                'text'             => 'Do you feel safe where you live?',
                'response_type'    => 'likert_5',
                'source_framework' => 'HBSC-LAC',
                'min_value'        => 1,
                'max_value'        => 5,
                'is_positive'      => 1,
                'risk_weight'      => 4.0,
                'risk_level'       => 'critical',
                'age_band_min'     => 11,
                'age_band_max'     => 18,
                'option_labels'    => json_encode(['Never', 'Rarely', 'Sometimes', 'Most of the time', 'Always']),
            ],
            // Q42
            [
                'domain_id'        => 6,
                'text'             => 'Do you feel safe at school?',
                'response_type'    => 'likert_5',
                'source_framework' => 'HBSC',
                'min_value'        => 1,
                'max_value'        => 5,
                'is_positive'      => 1,
                'risk_weight'      => 3.0,
                'risk_level'       => 'critical',
                'age_band_min'     => 11,
                'age_band_max'     => 18,
                'option_labels'    => json_encode(['Never', 'Rarely', 'Sometimes', 'Most of the time', 'Always']),
            ],
            // Q43
            [
                'domain_id'        => 6,
                'text'             => 'How often have you been bullied at school in the past couple of months?',
                'response_type'    => 'likert_5',
                'source_framework' => 'Olweus',
                'min_value'        => 1,
                'max_value'        => 5,
                'is_positive'      => 0,
                'risk_weight'      => 3.5,
                'risk_level'       => 'critical',
                'age_band_min'     => 11,
                'age_band_max'     => 18,
                'option_labels'    => json_encode(['I have not been bullied', 'Once or twice', '2 or 3 times a month', 'About once a week', 'Several times a week']),
            ],
            // Q44
            [
                'domain_id'        => 6,
                'text'             => 'In the past couple of months, how often have you been cyberbullied?',
                'response_type'    => 'likert_5',
                'source_framework' => 'Olweus',
                'min_value'        => 1,
                'max_value'        => 5,
                'is_positive'      => 0,
                'risk_weight'      => 3.0,
                'risk_level'       => 'high',
                'age_band_min'     => 11,
                'age_band_max'     => 18,
                'option_labels'    => json_encode(['I have not been cyberbullied', 'Once or twice', '2 or 3 times a month', 'About once a week', 'Several times a week']),
            ],
            // Q45
            [
                'domain_id'        => 6,
                'text'             => 'During the past 12 months, how many times were you in a physical fight?',
                'response_type'    => 'likert_5',
                'source_framework' => 'HBSC',
                'min_value'        => 1,
                'max_value'        => 5,
                'is_positive'      => 0,
                'risk_weight'      => 3.0,
                'risk_level'       => 'high',
                'age_band_min'     => 11,
                'age_band_max'     => 18,
                'option_labels'    => json_encode(['Not at all', '1 time', '2 times', '3 times', '4 times or more']),
            ],
            // Q46
            [
                'domain_id'        => 6,
                'text'             => 'Is there an adult you trust who you can go to if you feel unsafe?',
                'response_type'    => 'likert_3',
                'source_framework' => 'HBSC-LAC',
                'min_value'        => 1,
                'max_value'        => 3,
                'is_positive'      => 1,
                'risk_weight'      => 4.0,
                'risk_level'       => 'critical',
                'age_band_min'     => 11,
                'age_band_max'     => 18,
                'option_labels'    => json_encode(['No', 'I\'m not sure', 'Yes']),
            ],
        ];
    }

    // ── Domain 7: Life Satisfaction ───────────────────────────────────────────

    private function lifeSatisfactionQuestions(): array
    {
        return [
            // Q47
            [
                'domain_id'        => 7,
                'text'             => 'Imagine a ladder where the top (10) is the best possible life for you and the bottom (0) is the worst. Where do you feel you stand right now?',
                'response_type'    => 'slider',
                'source_framework' => 'Cantril',
                'min_value'        => 0,
                'max_value'        => 10,
                'is_positive'      => 1,
                'risk_weight'      => 2.5,
                'risk_level'       => 'high',
                'age_band_min'     => 11,
                'age_band_max'     => 18,
                'option_labels'    => null,
            ],
            // Q48
            [
                'domain_id'        => 7,
                'text'             => 'Overall, how happy do you feel with your life at the moment?',
                'response_type'    => 'emoji_scale',
                'source_framework' => 'CORS',
                'min_value'        => 1,
                'max_value'        => 5,
                'is_positive'      => 1,
                'risk_weight'      => 2.0,
                'risk_level'       => 'medium',
                'age_band_min'     => 11,
                'age_band_max'     => 18,
                'option_labels'    => json_encode(['Very unhappy', 'Unhappy', 'Okay', 'Happy', 'Very happy']),
            ],
            // Q49
            [
                'domain_id'        => 7,
                'text'             => 'How satisfied are you with yourself?',
                'response_type'    => 'likert_5',
                'source_framework' => 'CORS',
                'min_value'        => 1,
                'max_value'        => 5,
                'is_positive'      => 1,
                'risk_weight'      => 2.0,
                'risk_level'       => 'medium',
                'age_band_min'     => 11,
                'age_band_max'     => 18,
                'option_labels'    => json_encode(['Not at all', 'A little', 'Somewhat', 'Quite satisfied', 'Very satisfied']),
            ],
            // Q50
            [
                'domain_id'        => 7,
                'text'             => 'How often do you feel that your life is going well?',
                'response_type'    => 'likert_5',
                'source_framework' => 'CORS',
                'min_value'        => 1,
                'max_value'        => 5,
                'is_positive'      => 1,
                'risk_weight'      => 1.5,
                'risk_level'       => 'medium',
                'age_band_min'     => 11,
                'age_band_max'     => 18,
                'option_labels'    => json_encode(['Never', 'Rarely', 'Sometimes', 'Often', 'Always']),
            ],
        ];
    }
}
