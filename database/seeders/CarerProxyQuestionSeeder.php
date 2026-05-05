<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CarerProxyQuestionSeeder extends Seeder
{
    /**
     * Seeds one proxy observation question per domain.
     * All use:
     *   - response_type = likert_5
     *   - min_value = 0, max_value = 4
     *   - is_positive = 1 (higher raw value = better wellbeing)
     *   - risk_weight = 1.0
     *   - source_framework = 'carer_proxy'  ← used to look them up in controller
     *   - risk_level = 'low' (alerts handled by WellbeingAlertService, not question-level)
     *
     * Safe to run multiple times — skips existing proxy questions per domain.
     */
    public function run(): void
    {
        $domains = DB::table('domains')->pluck('id', DB::raw('LOWER(name)'))->toArray();

        $questions = [
            'emotional' => [
                'text'          => "How would you describe this child's general mood over the past week?",
                'option_labels' => json_encode(['Very low', 'Mostly low', 'Mixed', 'Mostly positive', 'Very positive']),
            ],
            'behavioural' => [
                'text'          => "How has the child's behaviour been at home over the past week?",
                'option_labels' => json_encode(['Very difficult', 'Frequent difficulties', 'Some difficulties', 'Mostly settled', 'Very settled']),
            ],
            'social' => [
                'text'          => "How well is the child interacting with others (family, peers)?",
                'option_labels' => json_encode(['Withdrawn', 'Poor', 'Some difficulty', 'Well', 'Very well']),
            ],
            'physical' => [
                'text'          => "How would you describe the child's sleep, appetite, and physical health this week?",
                'option_labels' => json_encode(['Very poor', 'Poor', 'Fair', 'Good', 'Very good']),
            ],
            'education' => [
                'text'          => "Is the child attending school or following their daily routine?",
                'option_labels' => json_encode(['Not attending', 'Frequent absences', 'Some absences', 'Mostly attending', 'Full attendance']),
            ],
            'safety' => [
                'text'          => "Do you have any concerns about the child's safety or wellbeing at home?",
                'option_labels' => json_encode(['Immediate concern', 'Significant concern', 'Moderate concern', 'Minor concern', 'No concerns']),
            ],
            'life satisfaction' => [
                'text'          => "How does the child seem to feel about their life overall?",
                'option_labels' => json_encode(['Very unhappy', 'Mostly unhappy', 'Mixed', 'Mostly happy', 'Very happy']),
            ],
        ];

        foreach ($questions as $domainName => $q) {
            $domainId = $domains[$domainName] ?? null;

            if (!$domainId) {
                $this->command->warn("Domain not found: {$domainName} — skipping.");
                continue;
            }

            // Skip if already seeded for this domain
            $exists = DB::table('questions')
                ->where('domain_id', $domainId)
                ->where('source_framework', 'carer_proxy')
                ->exists();

            if ($exists) {
                $this->command->info("Proxy question already exists for domain: {$domainName} — skipping.");
                continue;
            }

            DB::table('questions')->insert([
                'domain_id'        => $domainId,
                'text'             => $q['text'],
                'response_type'    => 'likert_5',
                'source_framework' => 'carer_proxy',
                'min_value'        => 0,
                'max_value'        => 4,
                'is_positive'      => 1,
                'risk_weight'      => 1.00,
                'risk_level'       => 'low',
                'age_band_min'     => null,
                'age_band_max'     => null,
                'is_active'        => 1,
                'version'          => 1,
                'option_labels'    => $q['option_labels'],
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);

            $this->command->info("Seeded proxy question for domain: {$domainName}");
        }
    }
}
