<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CaseFile;
use App\Models\User;

class CaseFileController extends Controller
{

    public function index()
    {
        $user = auth()->user();

        $cases = $user->socialWorkerCases()->with([
            'youngPerson',
            'wellbeingChecks',
        ])->get();

        return view('socialworker.cases.index', compact('cases'));
    }

    public function show(CaseFile $case)
    {
        $user = auth()->user();

        abort_if(! $case->users()->where('users.id', $user->id)->exists(), 403);


        $case->load([
            'youngPerson',
            'carers',
            'appointments'      => fn ($q) => $q->orderBy('start_time', 'desc'),
            'placements.carer',
            'medicalInfos',
            'educationInfos',
            'documents',
            'wellbeingChecks.domainScores.domain',
        ]);
    $tab = request('tab', 'child');
        $checkId = request('check');

        // Available carers for the assign form
        $availableCarers = User::where('role', 'carer')->orderBy('name')->get(['id', 'name']);

        return view('socialworker.cases.show', compact('case', 'availableCarers','tab', 'checkId'));
    }

    public function edit(CaseFile $case)
    {
        $children = User::where('role', 'young_person')->orderBy('name')->get();
        $carers   = User::where('role', 'carer')->orderBy('name')->get();

        return view('socialworker.case_edit', compact('case', 'children', 'carers'));
    }


    public function update(Request $request, CaseFile $case)
    {
        $validated = $request->validate([
            'young_person_id' => 'nullable|exists:users,id',
            'status'          => 'required|string|max:100',
            'risk_level'      => 'required|string|max:50',
            'carers'          => 'nullable|array',
            'carers.*'        => 'exists:users,id',
        ]);

        $case->update([
            'young_person_id' => $validated['young_person_id'] ?? null,
            'status'          => $validated['status'],
            'risk_level'      => $validated['risk_level'],
        ]);

        if (! empty($validated['carers'])) {
            $case->users()->syncWithPivotValues(
                $validated['carers'],
                ['role' => 'carer', 'assigned_at' => now()],
                false
            );
        }

        return redirect()
            ->route('socialworker.cases.show', $case)
            ->with('success', 'Case updated successfully.');
    }


    public function assignCarer(Request $request, CaseFile $case)
    {
        abort_if(auth()->user()->role !== 'social_worker', 403);

        $validated = $request->validate([
            'carer_id' => 'required|exists:users,id',
        ]);

        $case->users()->syncWithoutDetaching([
            $validated['carer_id'] => [
                'role'        => 'carer',
                'assigned_at' => now(),
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);

        $case->load('carers');

        return redirect()->back()->with('success', 'Carer assigned successfully.');
    }


    public function storePlacement(Request $request, CaseFile $case)
    {
        $validated = $request->validate([
            'type'       => 'required|string|max:100',
            'location'   => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
            'carer_id'   => 'nullable|exists:users,id',
            'capacity'   => 'nullable|integer|min:1',
            'notes'      => 'nullable|string',
        ]);

        $case->placements()->create(array_merge($validated, [
            'capacity'          => $validated['capacity'] ?? 1,
            'current_occupancy' => 0,
            'status'            => 'active',
        ]));

        return back()->with('success', 'Placement added successfully.');
    }

    public function storeMedical(Request $request, CaseFile $case)
    {
        $validated = $request->validate([
            'condition' => 'required|string|max:255',
            'notes'     => 'nullable|string',
        ]);

        $case->medicalInfos()->create($validated);

        return back()->with('success', 'Medical record added.');
    }


    public function storeEducation(Request $request, CaseFile $case)
    {
        $validated = $request->validate([
            'school_name' => 'required|string|max:255',
            'grade'       => 'nullable|string|max:50',
            'notes'       => 'nullable|string',
        ]);

        $case->educationInfos()->create($validated);

        return back()->with('success', 'Education record added.');
    }

    public function storeDocument(Request $request, CaseFile $case)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png,docx|max:10240',
        ]);

        $path = $request->file('file')->store('documents', 'public');

        $case->documents()->create([
            'name'        => $validated['name'],
            'file_path'   => $path,
            'uploaded_by' => auth()->id(),
        ]);

        return back()->with('success', 'Document uploaded successfully.');
    }
}
