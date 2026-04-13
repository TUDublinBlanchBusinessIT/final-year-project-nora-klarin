<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WellbeingCheck;
use Carbon\Carbon;

class WellbeingChecksSeeder extends Seeder
{
    public function run()
    {

        $data = [
            [
                'date' => Carbon::now()->subWeeks(6),
                'scores' => [
                    'overall_score' => 72,
                    'emotional_score' => 70,
                    'behavioural_score' => 75,
                    'physical_score' => 68,
                    'safety_score' => 80,
                    'school_score' => 73,
                    'relationship_score' => 73,
                ],
            ],
            [
                'date' => Carbon::now()->subWeeks(4),
                'scores' => [
                    'overall_score' => 65,
                    'emotional_score' => 60,
                    'behavioural_score' => 68,
                    'physical_score' => 64,
                    'safety_score' => 70,
                    'school_score' => 67,
                    'relationship_score' => 66,
                ],
            ],
            [
                'date' => Carbon::now()->subWeeks(2),
                'scores' => [
                    'overall_score' => 58,
                    'emotional_score' => 55,
                    'behavioural_score' => 60,
                    'physical_score' => 57,
                    'safety_score' => 62,
                    'school_score' => 57,
                    'relationship_score' => 55,
                ],
            ],
            [
                'date' => Carbon::now()->subWeek(),
                'scores' => [
                    'overall_score' => 82,
                    'emotional_score' => 80,
                    'behavioural_score' => 83,
                    'physical_score' => 85,
                    'safety_score' => 80,
                    'school_score' => 88,
                    'relationship_score' => 80,
                ],
            ],
            [
                'date' => Carbon::now(),
                'scores' => [
                    'overall_score' => 90,
                    'emotional_score' => 88,
                    'behavioural_score' => 92,
                    'physical_score' => 90,
                    'safety_score' => 95,
                    'school_score' => 87,
                    'relationship_score' => 88,
                ],
            ],
        ];

        foreach ($data as $item) {
            WellbeingCheck::create(array_merge([
                'case_file_id' => 1,
                'journal_notes' => 'Auto-generated test data',
                // risk_level can be derived dynamically in your model
            ], $item['scores'], [
                'created_at' => $item['date'],
                'updated_at' => $item['date'],
            ]));
        }
    }
}