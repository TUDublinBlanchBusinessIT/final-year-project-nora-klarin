<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\CaseFile;
use App\Models\Placement;
use App\Models\User;
use Illuminate\Http\Request;

class CaseFileController extends Controller
{
    // Show a case file
    public function show(CaseFile $case)
    {
        $user = auth()->user();

        // Ensure the user is assigned to this case
        $assigned = $case->users()->where('users.id', $user->id)->exists();
        abort_if(!$assigned, 403);

        $case->load([
            'youngPerson',
            'carers',
            'appointments',
            'placements.carer',  // eager load the carer for each placement
            'medicalInfos',
            'educationInfos',
            'documents'
        ]);

        return view('socialworker.casefile', compact('case'));
    }

    // Edit a case file
    public function edit(CaseFile $case)
    {
        $children = User::where('role', 'young_person')->get();
        $carers = User::where('role', 'carer')->get();

        return view('socialworker.case_edit', compact('case', 'children', 'carers'));
    }

    // Update basic case info and carers
    public function update(Request $request, CaseFile $case)
    {
        $request->validate([
            'young_person_id' => 'nullable|exists:users,id',
            'status' => 'required|string',
            'risk_level' => 'required|string',
        ]);

        $case->update([
            'young_person_id' => $request->young_person_id,
            'status' => $request->status,
            'risk_level' => $request->risk_level,
        ]);

        // Sync carers
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

    // Store a new placement
    public function storePlacement(Request $request, CaseFile $case)
    {
        $request->validate([
            'type' => 'required|string',
            'location' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'carer_id' => 'nullable|exists:users,id',
            'capacity' => 'nullable|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $case->placements()->create([
            'type' => $request->type,
            'location' => $request->location,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'carer_id' => $request->carer_id,
            'capacity' => $request->capacity ?? 1,
            'current_occupancy' => 0,
            'status' => 'active',
            'notes' => $request->notes,
        ]);

        return back()->with('success', 'Placement added successfully.');
    }

    // Store medical information
    public function storeMedical(Request $request, CaseFile $case)
    {
        $request->validate([
            'condition' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $case->medicalInfos()->create($request->all());

        return back()->with('success', 'Medical info added.');
    }

    // Store education information
    public function storeEducation(Request $request, CaseFile $case)
    {
        $request->validate([
            'school_name' => 'required|string',
            'grade' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $case->educationInfos()->create($request->all());

        return back()->with('success', 'Education info added.');
    }

    // Store documents
    public function storeDocument(Request $request, CaseFile $case)
    {
        $request->validate([
            'name' => 'required|string',
            'file' => 'required|file|mimes:pdf,jpg,png,docx|max:10240',
        ]);

        $path = $request->file('file')->store('documents', 'public');

        $case->documents()->create([
            'name' => $request->name,
            'file_path' => $path,
            'uploaded_by' => auth()->id(),
        ]);

        return back()->with('success', 'Document uploaded.');
    }
}