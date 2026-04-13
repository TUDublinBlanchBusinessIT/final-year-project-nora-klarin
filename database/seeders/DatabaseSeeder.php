<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            DomainAndTagSeeder::class,
            GoalTemplateSeeder::class,
            QuestionSeeder::class,
            QuestionTagSeeder::class,
            QuestionWordingsSeeder::class,
        ]);
    }
}