<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CaseFile;
use App\Models\User;
use App\Models\Question;
use App\Models\Domain;
use Illuminate\Support\Facades\DB;
use App\Services\WellbeingScoringService;
use App\Models\WellbeingCheck;

class WellbeingCheckController extends Controller
{
    public function create()
    {
        $domains = Domain::with(['questions' => function ($q) {
            $q->where('is_active', true);
        }])->get();

        return view('child.wellbeing.check', compact('domains'));
    }

    public function submit(Request $request)
    {
        $questions = Question::where('is_active', true)->get();

        $check = WellbeingCheck::create([
            'child_id' => auth()->id(),
            'completed_by_type' => 'child',
            'completed_by_user_id' => auth()->id(),
            'week_start' => now()->startOfWeek(),
        ]);

        $scoring = new WellbeingScoringService();

        foreach ($questions as $question) {

            $raw = $request->input("question_{$question->id}");

            if ($raw === null) continue;

            $score = $scoring->normalise(
                $raw,
                $question->min_value,
                $question->max_value,
                $question->is_positive
            );

            $risk = $scoring->riskContribution(
                $score,
                $question->risk_weight
            );

            $check->responses()->create([
                'question_id' => $question->id,
                'raw_value' => $raw,
                'normalised_score' => $score,
                'risk_contribution' => $risk
            ]);
        }

        $scoring->calculateDomainScores($check);

        $overallScore = $scoring->overallFromDomains($check);

        $overallRisk = $scoring->overallRiskFromDomains($check);

        $safeguarding = $scoring->analyseTagPatterns($check);

        $riskLevel = $scoring->classifyRisk($overallRisk);

        if ($safeguarding) {
            $riskLevel = 'critical';
        }

        $check->update([
            'overall_score' => $overallScore,
            'overall_risk_score' => $overallRisk,
            'risk_level' => $riskLevel
        ]);

        return redirect()->route('child.wellbeing.result', $check);

    }

    public function result(WellbeingCheck $check)
    {
        $check->load('domainScores.domain');

        return view('child.wellbeing.result', compact('check'));
    }

        public function alerts()
    {
        $user = auth()->user();

        abort_if($user->role !== 'social_worker', 403);

        $checks = \App\Models\WellbeingCheck::with('child')
            ->whereIn('risk_level', ['high', 'critical'])
            ->orderByDesc('week_start')
            ->get()
            ->groupBy('child_id');

        return view('socialworker.wellbeing.alerts', compact('checks'));
    }
}
