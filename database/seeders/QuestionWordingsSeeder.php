<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionWordingsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('question_wordings')->insert([

            [
                'question_id'=>2,
                'age_min'=>11,
                'age_max'=>13,
                'text'=>'Have you been feeling sad or down?',
                'created_at'=>now(),
                'updated_at'=>now(),
            ],
            [
                'question_id'=>2,
                'age_min'=>14,
                'age_max'=>18,
                'text'=>'How often have you felt low or down?',
                'created_at'=>now(),
                'updated_at'=>now(),
            ],

            [
                'question_id'=>4,
                'age_min'=>11,
                'age_max'=>13,
                'text'=>'Do you feel lonely?',
                'created_at'=>now(),
                'updated_at'=>now(),
            ],
            [
                'question_id'=>4,
                'age_min'=>14,
                'age_max'=>18,
                'text'=>'How often do you feel lonely?',
                'created_at'=>now(),
                'updated_at'=>now(),
            ],

            [
                'question_id'=>10,
                'age_min'=>11,
                'age_max'=>13,
                'text'=>'Do you feel in control of what you do?',
                'created_at'=>now(),
                'updated_at'=>now(),
            ],
            [
                'question_id'=>10,
                'age_min'=>14,
                'age_max'=>18,
                'text'=>'How often do you feel in control of your behaviour?',
                'created_at'=>now(),
                'updated_at'=>now(),
            ],

            [
                'question_id'=>18,
                'age_min'=>11,
                'age_max'=>13,
                'text'=>'Do you feel safe where you live?',
                'created_at'=>now(),
                'updated_at'=>now(),
            ],
            [
                'question_id'=>18,
                'age_min'=>14,
                'age_max'=>18,
                'text'=>'Do you feel safe at home?',
                'created_at'=>now(),
                'updated_at'=>now(),
            ],

        ]);
    }
}