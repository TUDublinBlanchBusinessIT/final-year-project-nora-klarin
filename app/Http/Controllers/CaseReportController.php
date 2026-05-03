<?php

namespace App\Http\Controllers;

use App\Models\CaseFile;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class CaseReportController extends Controller
{
    public function show(CaseFile $case)
    {
        abort_if(
            !$case->users()->where('users.id', auth()->id())->exists(),
            403
        );

        $case->load([
            'youngPerson',
            'medicalInfos',
            'educationInfos',
            'placements',
            'wellbeingChecks.domainScores.domain',
        ]);

        // ── Wellbeing data ──────────────────────────────────────────────
        $checks = $case->wellbeingChecks->sortBy('created_at');

        $checks->each(function ($check) {
            $byDomain = $check->domainScores
                ->keyBy(fn($s) => strtolower(trim($s->domain->name ?? '')));

            $check->emotional_score         = round($byDomain['emotional']?->average_score         ?? 0, 1);
            $check->behavioural_score       = round($byDomain['behavioural']?->average_score       ?? 0, 1);
            $check->social_score            = round($byDomain['social']?->average_score            ?? 0, 1);
            $check->physical_score          = round($byDomain['physical']?->average_score          ?? 0, 1);
            $check->education_score         = round($byDomain['education']?->average_score         ?? 0, 1);
            $check->safety_score            = round($byDomain['safety']?->average_score            ?? 0, 1);
            $check->life_satisfaction_score = round($byDomain['life satisfaction']?->average_score ?? 0, 1);
        });

        $latestCheck  = $checks->last();
        $previousCheck = $checks->count() >= 2 ? $checks->nth(2)->last() : null;

        // Domain trend (latest vs previous)
        $domainTrend = [];
        if ($latestCheck && $previousCheck) {
            foreach (['emotional', 'behavioural', 'social', 'physical', 'education', 'safety'] as $d) {
                $key     = $d . '_score';
                $current = $latestCheck->$key  ?? 0;
                $prev    = $previousCheck->$key ?? 0;
                $domainTrend[$d] = [
                    'current' => $current,
                    'change'  => round($current - $prev, 1),
                ];
            }
        }

        // ── Goals & missions ────────────────────────────────────────────
        $activeGoals = DB::table('case_goals')
            ->join('goals', 'case_goals.goal_id', '=', 'goals.id')
            ->leftJoin('domains', 'goals.source_domain_id', '=', 'domains.id')
            ->leftJoin('users as approver', 'goals.approved_by', '=', 'approver.id')
            ->where('case_goals.case_file_id', $case->id)
            ->whereIn('case_goals.status', ['in_progress', 'completed'])
            ->select(
                'case_goals.id as case_goal_id',
                'case_goals.status',
                'case_goals.due_date',
                'case_goals.child_accepted_at',
                'case_goals.updated_at as completed_at',
                'goals.title',
                'goals.description',
                'goals.approved_at',
                'domains.name as domain_name',
                'approver.name as approved_by_name',
            )
            ->orderByRaw("FIELD(case_goals.status,'in_progress','completed')")
            ->get();

        $caseGoalIds = $activeGoals->pluck('case_goal_id');

        $tasksByCaseGoal = DB::table('tasks')
            ->whereIn('case_goal_id', $caseGoalIds)
            ->where('child_visible', true)
            ->select('id', 'case_goal_id', 'title', 'description', 'completed_at', 'ai_suggested')
            ->get()
            ->groupBy('case_goal_id');

        // ── Engagement stats ────────────────────────────────────────────
        $totalMissions  = $tasksByCaseGoal->flatten()->count();
        $doneMissions   = $tasksByCaseGoal->flatten()->whereNotNull('completed_at')->count();
        $engagementPct  = $totalMissions > 0 ? round(($doneMissions / $totalMissions) * 100) : 0;

        $goalsCompleted = $activeGoals->where('status', 'completed')->count();
        $goalsActive    = $activeGoals->where('status', 'in_progress')->count();
        $goalsAccepted  = $activeGoals->whereNotNull('child_accepted_at')->count();

        $data = compact(
            'case',
            'checks',
            'latestCheck',
            'domainTrend',
            'activeGoals',
            'tasksByCaseGoal',
            'totalMissions',
            'doneMissions',
            'engagementPct',
            'goalsCompleted',
            'goalsActive',
            'goalsAccepted',
        );

        $pdf = Pdf::loadView('socialworker.reports.case_report', $data)
            ->setPaper('a4', 'portrait');

        return $pdf->stream('case-report-' . ($case->case_reference ?? $case->id) . '.pdf');
    }
}