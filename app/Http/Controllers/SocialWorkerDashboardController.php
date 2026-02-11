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

        $cases = $user->socialWorkerCases()->get();

        return view('socialworker.dashboard', compact('cases'));
    }

    public function show(CaseFile $case)
{
    $user = auth()->user();

    $assigned = $case->users()->where('users.id', $user->id)->exists();
    abort_if(!$assigned, 403);

    $case->load(['youngPerson', 'carers', 'appointments']);

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

    // Update case fields
    $case->update([
        'young_person_id' => $request->young_person_id,
        'status' => $request->status,
        'risk_level' => $request->risk_level,
    ]);

if ($request->has('carers')) {
    $case->users()->syncWithPivotValues(
        $request->carers,  // array of user IDs
        ['role' => 'carer', 'assigned_at' => now()],
        false // don't detach other roles
    );
}

    return redirect()
        ->route('socialworker.case.show', $case)
        ->with('success', 'Case updated successfully.');
}


}
