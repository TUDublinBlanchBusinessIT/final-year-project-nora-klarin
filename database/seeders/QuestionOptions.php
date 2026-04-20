<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuestionOptions extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
                DB::table('questions')->insert([
            
        'option_labels' => ['Never', 'Rarely', 'Sometimes', 'Most nights', 'Every night'],

        'option_labels' => ['Terrible', 'Not great', 'Okay', 'Pretty good', 'Amazing'],

        'option_labels' => ['No', 'Sometimes', 'Yes']
            ]);
            
    }
}
