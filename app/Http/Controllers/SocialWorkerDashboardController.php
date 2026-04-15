<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\CaseFile;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Message;
class SocialWorkerDashboardController extends Controller
{
public function index()
{
    $user = Auth::user();

    abort_if($user->role !== 'social_worker', 403);

    $cases = $user->socialWorkerCases()->with([
        'youngPerson',
        'wellbeingChecks.domainScores.domain'
    ])->get();

    $wellbeingChecks = $cases->flatMap(function ($case) {
        return $case->wellbeingChecks;
    })->sortBy('created_at');

    $wellbeingData = [];

    foreach ($cases as $case) {
        $child = $case->youngPerson;
        $checks = $case->wellbeingChecks->sortByDesc('week_start')->take(8);
        if ($checks->isEmpty()) continue;

        $latestCheck = $checks->first();

        $domainScores = $latestCheck->domainScores->mapWithKeys(function($ds){
            return [$ds->domain->name => $ds->average_score ?? 0];
        });

        $wellbeingData[] = [
            'child' => $child,
            'check' => $latestCheck,
            'checks' => $checks,
            'domainScores' => $domainScores
        ];
    }

    $placements = \App\Models\Placement::select(
            'id',
            'location',
            'type',
            'latitude',
            'longitude'
        )
        ->whereNotNull('latitude')
        ->whereNotNull('longitude')
        ->get();
    $user = auth()->user();

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
    ->get(['id', 'name', 'email', 'role'])
    ->map(function ($partner) use ($user) {

        $last = Message::query()
            ->where(function ($q) use ($user, $partner) {
                $q->where('sender_id', $user->id)
                  ->where('recipient_id', $partner->id);
            })
            ->orWhere(function ($q) use ($user, $partner) {
                $q->where('sender_id', $partner->id)
                  ->where('recipient_id', $user->id);
            })
            ->latest()
            ->first();

        $unread = Message::where('sender_id', $partner->id)
            ->where('recipient_id', $user->id)
            ->whereNull('read_at')
            ->count();

        $partner->last_body = $last?->body;
        $partner->unread_count = $unread;

        return $partner;
    });

    return view('socialworker.dashboard', compact(
        'cases',
        'wellbeingData',
        'wellbeingChecks',
        'placements',
        'conversations'
    ));
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
            ->route('socialworker.case.show', $case)
            ->with('success', 'Case updated successfully.');
    }
}
