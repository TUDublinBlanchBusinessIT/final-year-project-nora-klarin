<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\CaseFile;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\WellbeingCheck;
use App\Models\WellbeingDomainScore;
use Illuminate\Support\Facades\DB;
class SocialWorkerDashboardController extends Controller
{
public function index()
{
    $user = Auth::user();

    abort_if($user->role !== 'social_worker', 403);

    $cases = $user->socialWorkerCases()->with([
        'youngPerson',
        'wellbeingChecks.domainScores.domain',
        'appointments'
    ])->get();

$alerts = collect();

foreach ($cases as $case) {

    // High risk alert
    if (strtolower($case->risk_level) === 'high') {
        $alerts->push([
            'type' => 'high_risk_case',
            'message' => "High-risk case: " . ($case->youngPerson->name ?? 'Unknown'),
            'case_id' => $case->id,
            'route' => route('socialworker.cases.show', $case),
            'tab' => null,
        ]);
    }

    // Wellbeing decline alert
    $checks = $case->wellbeingChecks->sortByDesc('created_at')->values();

    if ($checks->count() >= 2) {
        $current = $checks[0]->overall_score;
        $previous = $checks[1]->overall_score;

        if ($current < $previous - 20) {
            $alerts->push([
                'type' => 'wellbeing_decline',
                'message' => ($case->youngPerson->name ?? 'Unknown') . " has a significant wellbeing decline",
                'case_id' => $case->id,
                'route' => route('socialworker.cases.show', [
                    'case' => $case,
                    'tab' => 'wellbeing',
                    'check' => $checks[0]->id,
                ]),
            ]);
        }
    }
}
    $partnerIds = Message::query()
        ->where('sender_id', $user->id)
        ->orWhere('recipient_id', $user->id)
        ->get(['sender_id', 'recipient_id'])
        ->flatMap(fn ($m) => [$m->sender_id, $m->recipient_id])
        ->unique()
        ->reject(fn ($id) => $id == $user->id)
        ->values();

    $conversations = User::query()
        ->whereIn('id', $partnerIds)
        ->get()
        ->map(function ($partner) use ($user) {

            $last = Message::query()
                ->where(fn ($q) =>
                    $q->where('sender_id', $user->id)
                      ->where('recipient_id', $partner->id)
                )
                ->orWhere(fn ($q) =>
                    $q->where('sender_id', $partner->id)
                      ->where('recipient_id', $user->id)
                )
                ->latest()
                ->first();

            $partner->last_body = $last?->body;

            $partner->unread_count = Message::where('sender_id', $partner->id)
                ->where('recipient_id', $user->id)
                ->whereNull('read_at')
                ->count();

            return $partner;
        });

    // -------------------------
    // Placements (map)
    // -------------------------
    $placements = \App\Models\Placement::select(
        'id','location','type','latitude','longitude'
    )
    ->whereNotNull('latitude')
    ->whereNotNull('longitude')
    ->get();

    // -------------------------
    // Wellbeing summary (latest per case)
    // -------------------------
    $wellbeingData = [];

    foreach ($cases as $case) {
        $child = $case->youngPerson;
        $checks = $case->wellbeingChecks->sortByDesc('created_at')->take(8);

        if ($checks->isEmpty()) continue;

        $latestCheck = $checks->first();

        $domainScores = $latestCheck->domainScores->mapWithKeys(function ($ds) {
            return [$ds->domain->name => $ds->average_score ?? 0];
        });

        $wellbeingData[] = [
            'child' => $child,
            'check' => $latestCheck,
            'checks' => $checks,
            'domainScores' => $domainScores
        ];

    }

    // -------------------------
    // Trend data (for charts)
    // -------------------------
    $wellbeingTrendData = $cases->map(function ($case) {
        return [
            'case_id' => $case->id,
            'name' => $case->youngPerson->name ?? 'Unknown',
            'scores' => $case->wellbeingChecks
                ->sortBy('created_at')
                ->map(fn ($check) => [
                    'date' => $check->created_at?->format('Y-m-d'),
                    'score' => $check->overall_score ?? 0,
                ])->values()
        ];
    });

    // -------------------------
    // Domain averages
    // -------------------------
    $domainScores = DB::table('wellbeing_domain_scores')
        ->join('domains', 'domains.id', '=', 'wellbeing_domain_scores.domain_id')
        ->select(
            'domains.name as domain',
            DB::raw('AVG(average_score) as avg_score'),
            DB::raw('AVG(risk_score) as avg_risk')
        )
        ->groupBy('domains.name')
        ->get();

    return view('socialworker.dashboard', compact(
        'cases',
        'alerts',
        'conversations',
        'placements',
        'wellbeingData',
        'wellbeingTrendData',
        'domainScores', ))->with('tab', 'chart');
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
            'placements'
        ]);

        return view('socialworker.casefile', compact('case'));
    }

public function edit(CaseFile $case)
    {
        $children = User::where('role', 'young_person')->get();
        $carers = User::where('role', 'carer')->get();

        return view('socialworker.case_edit', compact('case', 'children', 'carers'));
    }

public function update(Request $request, CaseFile $case)
    {
        $request->validate([
            'young_person_id' => 'nullable|exists:users,id',
            'status' => 'required|string',
            'risk_level' => 'required|string',
            'carer_id' => 'nullable|exists:users,id'

        ]);

        $case->update([
            'young_person_id' => $request->young_person_id,
            'status' => $request->status,
            'risk_level' => $request->risk_level,
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
