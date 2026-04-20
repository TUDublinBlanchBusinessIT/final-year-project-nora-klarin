<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\CaseFile;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Http\Request;


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

    return view('socialworker.dashboard', compact('cases', 'wellbeingData'));
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
