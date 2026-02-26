<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CaseFile;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CaseFileController extends Controller
{
  
    public function show(CaseFile $case)
    {
        $user = auth()->user();

        // Ensure the user is assigned to this case
        abort_if(!$case->users()->where('users.id', $user->id)->exists(), 403);

        $case->load([
            'youngPerson',
            'carers',
            'appointments' => fn($q) => $q->orderBy('start_time', 'desc'),
            'placements.carer',
            'medicalInfos',
            'educationInfos',
            'documents',
        ]);

        return view('socialworker.casefile', compact('case'));
    }

    /**
     * Show form to edit a case file.
     */
    public function edit(CaseFile $case)
    {
        $children = User::where('role', 'young_person')->get();
        $carers   = User::where('role', 'carer')->get();

        return view('socialworker.case_edit', compact('case', 'children', 'carers'));
    }

    /**
     * Update case basic info and assigned carers.
     */
    public function update(Request $request, CaseFile $case)
    {
        $validated = $request->validate([
            'young_person_id' => 'nullable|exists:users,id',
            'status'          => 'required|string',
            'risk_level'      => 'required|string',
            'carers'          => 'nullable|array',
            'carers.*'        => 'exists:users,id',
        ]);

        $case->update([
            'young_person_id' => $validated['young_person_id'] ?? null,
            'status'          => $validated['status'],
            'risk_level'      => $validated['risk_level'],
        ]);

        if (!empty($validated['carers'])) {
            $case->users()->syncWithPivotValues(
                $validated['carers'],
                ['role' => 'carer', 'assigned_at' => now()],
                false
            );
        }

        return redirect()
            ->route('socialworker.case.show', $case)
            ->with('success', 'Case updated successfully.');
    }

  
public function assignCarer(Request $request, CaseFile $case)
{
    abort_if(auth()->user()->role !== 'social_worker', 403);

    $validated = $request->validate([
        'carer_id' => 'required|exists:users,id',
    ]);

    $carerId = $validated['carer_id'];

    // Safely assign carer without violating unique constraint
    $case->users()->syncWithoutDetaching([
        $carerId => [
            'role'        => 'carer',
            'assigned_at' => now(),
            'created_at'  => now(),
            'updated_at'  => now(),
        ]
    ]);

    // Reload carers relation so Blade sees it
    $case->load('carers');

    return redirect()->back()->with('success', 'Carer assigned successfully.');
}

    public function storePlacement(Request $request, CaseFile $case)
    {
        $validated = $request->validate([
            'type'       => 'required|string',
            'location'   => 'required|string',
            'start_date' => 'required|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
            'carer_id'   => 'nullable|exists:users,id',
            'capacity'   => 'nullable|integer|min:1',
            'notes'      => 'nullable|string',
        ]);

        $case->placements()->create(array_merge($validated, [
            'capacity'         => $validated['capacity'] ?? 1,
            'current_occupancy'=> 0,
            'status'           => 'active',
        ]));

        return back()->with('success', 'Placement added successfully.');
    }


    public function storeMedical(Request $request, CaseFile $case)
    {
        $validated = $request->validate([
            'condition' => 'required|string',
            'notes'     => 'nullable|string',
        ]);

        $case->medicalInfos()->create($validated);

        return back()->with('success', 'Medical info added.');
    }

   
    public function storeEducation(Request $request, CaseFile $case)
    {
        $validated = $request->validate([
            'school_name' => 'required|string',
            'grade'       => 'nullable|string',
            'notes'       => 'nullable|string',
        ]);

        $case->educationInfos()->create($validated);

        return back()->with('success', 'Education info added.');
    }


    public function storeDocument(Request $request, CaseFile $case)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'file' => 'required|file|mimes:pdf,jpg,png,docx|max:10240',
        ]);

        $path = $request->file('file')->store('documents', 'public');

        $case->documents()->create([
            'name'        => $validated['name'],
            'file_path'   => $path,
            'uploaded_by' => auth()->id(),
        ]);

        return back()->with('success', 'Document uploaded.');
    }
}