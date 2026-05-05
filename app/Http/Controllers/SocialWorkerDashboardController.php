<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\CaseFile;
use App\Models\Alert;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\WellbeingCheck;
use App\Models\DomainScore;
use Illuminate\Support\Facades\DB;

class SocialWorkerDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        abort_if($user->role !== 'social_worker', 403);

        // ── Cases with all needed relations ──────────────────────────────────
        $cases = CaseFile::whereHas('users', function ($q) use ($user) {
            $q->where('case_user.user_id', $user->id)
              ->where('case_user.role', 'social_worker');
        })->with([
            'youngPerson',   // FIX: now scoped to role=young_person via the model relation
            'users',
            'appointments',
            'wellbeingChecks.domainScores.domain',
        ])->get();

        // ── Alerts ───────────────────────────────────────────────────────────
        $youngPersonIds = $cases->pluck('young_person_id')->filter()->unique()->values();
        $alerts = Alert::whereIn('young_person_id', $youngPersonIds)
            ->whereNull('acknowledged_at')
            ->whereIn('alert_type', ['tag_override', 'critical_response'])
            ->with(['wellbeingCheck.caseFile'])
            ->orderByRaw("FIELD(severity, 'critical', 'high', 'medium', 'low')")
            ->orderByDesc('created_at')
            ->get()
            ->map(function (Alert $a) {
                $caseId = $a->wellbeingCheck?->case_file_id;
                return [
                    'id'       => $a->id,
                    'type'     => $a->alert_type,
                    'severity' => $a->severity,
                    'message'  => $a->message,
                    'case_id'  => $caseId,
                    'route'    => $caseId
                        ? route('socialworker.cases.show', $caseId)
                        : route('socialworker.dashboard'),
                ];
            });

        // ── Messages / conversations ──────────────────────────────────────────
        $partnerIds = Message::where('sender_id', $user->id)
            ->orWhere('recipient_id', $user->id)
            ->get(['sender_id', 'recipient_id'])
            ->flatMap(fn($m) => [$m->sender_id, $m->recipient_id])
            ->unique()
            ->reject(fn($id) => $id == $user->id)
            ->values();

        $conversations = User::whereIn('id', $partnerIds)->get()
            ->map(function ($partner) use ($user) {
                $last = Message::where(fn($q) =>
                        $q->where('sender_id', $user->id)->where('recipient_id', $partner->id)
                    )->orWhere(fn($q) =>
                        $q->where('sender_id', $partner->id)->where('recipient_id', $user->id)
                    )->latest()->first();

                $partner->last_body    = $last?->body;
                $partner->unread_count = Message::where('sender_id', $partner->id)
                    ->where('recipient_id', $user->id)
                    ->whereNull('read_at')
                    ->count();
                return $partner;
            });

        // ── Placements map ────────────────────────────────────────────────────
        $placements = \App\Models\Placement::select('id', 'location', 'type', 'latitude', 'longitude')
            ->whereNotNull('latitude')->whereNotNull('longitude')->get();

        // ── Wellbeing summary (latest COMPLETED check per case) ───────────────
        $wellbeingData = [];
        foreach ($cases as $case) {
            $child = $case->youngPerson;

            // FIX: only include completed checks (completed_at not null), sorted by
            // completed_at so we always get the genuine latest submitted check.
            $checks = $case->wellbeingChecks
                ->filter(fn($c) => $c->completed_at !== null)
                ->sortByDesc('completed_at')
                ->take(8);

            if ($checks->isEmpty()) continue;

            $latestCheck  = $checks->first();
            $domainScores = $latestCheck->domainScores->mapWithKeys(
                fn($ds) => [$ds->domain->name ?? 'Unknown' => $ds->average_score ?? 0]
            );

            $wellbeingData[] = [
                'child'        => $child,
                'check'        => $latestCheck,
                'checks'       => $checks,
                'domainScores' => $domainScores,
            ];
        }

        // ── Trend data ────────────────────────────────────────────────────────
$wellbeingTrendData = $cases->map(function ($case) {
    $points = $case->wellbeingChecks
        ->filter(fn($c) => $c->completed_at !== null && $c->overall_score !== null)
        ->sortBy('completed_at')
        ->map(fn($c) => [
            'x' => $c->completed_at->format('Y-m-d'),
            'y' => (int) $c->overall_score,
        ])->values()->toArray();

    return [
        'name'   => $case->youngPerson?->name ?? ('Case #' . $case->id),
        'points' => $points,
    ];
})->filter(fn($item) => count($item['points']) > 0)->values();

        // ── Domain averages ───────────────────────────────────────────────────
        $caseIds = $cases->pluck('id')->toArray();

        $domainScores = DB::table('wellbeing_domain_scores as ds')
            ->join('wellbeing_checks as wc', 'ds.wellbeing_check_id', '=', 'wc.id')
            ->join('domains as d', 'd.id', '=', 'ds.domain_id')
            ->whereIn('wc.case_file_id', $caseIds)
            ->whereNotNull('wc.completed_at')
            ->select('d.name', DB::raw('AVG(ds.average_score) as avg_score'))
            ->groupBy('d.id', 'd.name')
            ->orderBy('d.name')
            ->get();

        $currentWeekStart  = now()->subDays(7);
        $previousWeekStart = now()->subDays(14);

        $currentWeekDomain = DB::table('wellbeing_domain_scores as ds')
            ->join('wellbeing_checks as wc', 'ds.wellbeing_check_id', '=', 'wc.id')
            ->join('domains as d', 'd.id', '=', 'ds.domain_id')
            ->whereIn('wc.case_file_id', $caseIds)
            ->where('wc.completed_at', '>=', $currentWeekStart)
            ->select('d.name', DB::raw('AVG(ds.average_score) as avg_score'))
            ->groupBy('d.id', 'd.name')
            ->pluck('avg_score', 'name');

        $previousWeekDomain = DB::table('wellbeing_domain_scores as ds')
            ->join('wellbeing_checks as wc', 'ds.wellbeing_check_id', '=', 'wc.id')
            ->join('domains as d', 'd.id', '=', 'ds.domain_id')
            ->whereIn('wc.case_file_id', $caseIds)
            ->whereBetween('wc.completed_at', [$previousWeekStart, $currentWeekStart])
            ->select('d.name', DB::raw('AVG(ds.average_score) as avg_score'))
            ->groupBy('d.id', 'd.name')
            ->pluck('avg_score', 'name');

        $domainWeekChanges = $currentWeekDomain->map(function ($avg, $domain) use ($previousWeekDomain) {
            $previous = $previousWeekDomain->get($domain);
            if ($previous === null) return null;
            return [
                'label'  => $domain,
                'change' => round($avg - $previous, 2),
            ];
        })->filter()->sortByDesc(fn($item) => abs($item['change']))->values()->take(3);

        // ── Upcoming appointments ─────────────────────────────────────────────
        $upcomingAppointments = collect();
        foreach ($cases as $case) {
            if ($case->relationLoaded('appointments')) {
                foreach ($case->appointments as $appt) {
                    if ($appt->start_time >= now() && $appt->start_time <= now()->addDays(7)) {
                        $appt->case = $case;
                        $upcomingAppointments->push($appt);
                    }
                }
            }
        }
        $upcomingAppointments = $upcomingAppointments->sortBy('start_time')->take(8);

        $riskColours = [
            'critical' => '#ef4444',  // red-500
            'high'     => '#f97316',  // orange-500
            'medium'   => '#eab308',  // yellow-500
            'moderate' => '#eab308',  // alias — scoring uses 'moderate', case uses 'medium'
            'low'      => '#22c55e',  // green-500
        ];

        $casesByRisk    = collect();
        $caseRiskDetail = [];   

        foreach ($cases as $case) {
            $latestCheck = WellbeingCheck::where('case_file_id', $case->id)
                ->whereNotNull('completed_at')          
                ->orderByDesc('completed_at')
                ->first();

            $risk = $latestCheck?->risk_level ?? $case->risk_level ?? 'low';

            $casesByRisk[$risk] = ($casesByRisk[$risk] ?? 0) + 1;

            $caseRiskDetail[] = [
                'name'           => $case->youngPerson?->name ?? ('Case #' . $case->id),
                'risk'           => $risk,
                'score'          => $latestCheck ? round($latestCheck->overall_score ?? 0) : null,
                'last_check'     => $latestCheck?->completed_at?->format('d M Y'),
                'days_since'     => $latestCheck?->completed_at?->diffInDays(now()),
            ];
        }

        // Sort risk labels in severity order for consistent chart rendering
        $riskOrder      = ['critical', 'high', 'medium', 'moderate', 'low'];
        $sortedRisk     = collect($riskOrder)
            ->filter(fn($r) => $casesByRisk->has($r))
            ->mapWithKeys(fn($r) => [$r => $casesByRisk[$r]]);

        $chartData = [
'trendsOverTime' => [
    'datasets' => $wellbeingTrendData->map(function ($item, $index) {
        $palette = ['#818cf8','#34d399','#fb923c','#f472b6','#60a5fa','#a78bfa','#2dd4bf','#facc15'];
        return [
            'label'       => $item['name'],
            'data'        => $item['points'],  // {x: 'YYYY-MM-DD', y: score}
            'borderColor' => $palette[$index % 8],
        ];
    })->toArray(),
],

            'trendsByDomain' => [
                'labels'   => $domainScores->pluck('name')->toArray(),
                'datasets' => [[
                    'label'           => 'Average Score by Domain',
                    'data'            => $domainScores->pluck('avg_score')->map(fn($v) => round($v, 2))->toArray(),
                    // FIX: colour each bar by its score value (green ≥70, yellow ≥40, red <40)
                    'backgroundColor' => $domainScores->pluck('avg_score')->map(function ($score) {
                        if ($score >= 70) return '#22c55e';
                        if ($score >= 40) return '#eab308';
                        return '#ef4444';
                    })->toArray(),
                ]],
            ],

            // FIX: colours now keyed to the sorted risk labels, not positional
            'casesByRisk' => [
                'labels'   => $sortedRisk->keys()->map(fn($r) => ucfirst($r))->values()->toArray(),
                'datasets' => [[
                    'label'           => 'Cases by Risk',
                    'data'            => $sortedRisk->values()->toArray(),
                    'backgroundColor' => $sortedRisk->keys()
                        ->map(fn($r) => $riskColours[$r] ?? '#94a3b8')
                        ->values()->toArray(),
                ]],
            ],
        ];

        return view('socialworker.dashboard', compact(
            'cases',
            'alerts',
            'conversations',
            'placements',
            'wellbeingData',
            'wellbeingTrendData',
            'domainScores',
            'domainWeekChanges',
            'upcomingAppointments',
            'casesByRisk',
            'caseRiskDetail',   // new — available in view for richer display
            'chartData',
        ));
    }

    // ── Mark case as reviewed ──────────────────────────────────────────────────
    public function markReviewed(\App\Models\CaseFile $case)
    {
        abort_if(!$case->users()->where('users.id', auth()->id())->exists(), 403);
        $case->update(['last_reviewed_at' => now()]);
        return redirect()->back()->with('success', 'Case marked as reviewed.');
    }

    public function show(CaseFile $case)
    {
        $user = auth()->user();

        $assigned = $case->users()->where('users.id', $user->id)->exists();
        abort_if(!$assigned, 403);

        $case->load([
            'youngPerson',
            'carers',
            'appointments',
            'placements',
        ]);

        return view('socialworker.casefile', compact('case'));
    }

    public function edit(CaseFile $case)
    {
        $children = User::where('role', 'young_person')->get();
        $carers   = User::where('role', 'carer')->get();

        return view('socialworker.case_edit', compact('case', 'children', 'carers'));
    }

    public function update(Request $request, CaseFile $case)
    {
        $request->validate([
            'young_person_id' => 'nullable|exists:users,id',
            'status'          => 'required|string',
            'risk_level'      => 'required|string',
            'carer_id'        => 'nullable|exists:users,id',
        ]);

        $case->update([
            'young_person_id' => $request->young_person_id,
            'status'          => $request->status,
            'risk_level'      => $request->risk_level,
        ]);

        if ($request->has('carers')) {
            $case->users()->syncWithPivotValues(
                $request->carers,
                ['role' => 'carer', 'assigned_at' => now()],
                false
            );
        }

        return redirect()
            ->route('socialworker.cases.show', $case)
            ->with('success', 'Case updated successfully.');
    }
}