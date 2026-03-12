<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WellbeingCheck;
use Auth;

class ChildWellbeingController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'emotional_score' => 'required|integer|min:0|max:100',
            'behavioural_score' => 'required|integer|min:0|max:100',
            'physical_score' => 'required|integer|min:0|max:100',
            'safety_score' => 'required|integer|min:0|max:100',
            'school_score' => 'required|integer|min:0|max:100',
            'relationships_score' => 'required|integer|min:0|max:100',
            'journal_notes' => 'nullable|string',
        ]);

        $caseFileId = Auth::user()->childCase?->id; // assuming a relationship: child has one case
        if(!$caseFileId) {
            return back()->withErrors('No case assigned to you.');
        }

        $overall = round(array_sum($validated)/6);

        WellbeingCheck::create([
            'case_file_id' => $caseFileId,
            'overall_score' => $overall,
            'emotional_score' => $validated['emotional_score'],
            'behavioural_score' => $validated['behavioural_score'],
            'physical_score' => $validated['physical_score'],
            'safety_score' => $validated['safety_score'],
            'school_score' => $validated['school_score'],
            'relationship_score' => $validated['relationships_score'],
            'journal_notes' => $validated['journal_notes'],
        ]);

        return back()->with('wellbeing_success', 'Your wellbeing check has been submitted!');
    }
}