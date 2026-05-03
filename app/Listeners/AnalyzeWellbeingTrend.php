<?php

namespace App\Listeners;

use App\Events\WellbeingCheckCompleted;
use App\Models\WellbeingTrend;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AnalyzeWellbeingTrend implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(WellbeingCheckCompleted $event): void
    {
        $check = $event->check;
        $summary = $event->summary;

        WellbeingTrend::create([
            'case_file_id'      => $check->case_file_id,
            'wellbeing_check_id'=> $check->id,
            'overall_score'     => $summary['overall_wb_score'],
            'overall_risk_score'=> $summary['overall_risk_score'],
            'risk_level'        => $summary['risk_classification'],
            'trend_data'        => [
                'domain_scores' => $summary['domain_scores']->mapWithKeys(fn($ds) => [
                    $ds->domain->name => $ds->average_score,
                ])->toArray(),
                'safeguarding_triggered' => $summary['safeguarding_triggered'],
            ],
        ]);
    }
}
