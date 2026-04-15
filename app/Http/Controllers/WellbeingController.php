<?php



namespace App\Http\Controllers;



use Illuminate\Http\Request;

use App\Models\WellbeingCheck;



class WellbeingController extends Controller

{

    public function store(Request $request)

    {

        $request->validate([

            'case_file_id' => 'required|integer',

            'overall_score' => 'required|integer|min:1|max:10',

            'emotional_score' => 'required|integer|min:1|max:10',

            'behavioural_score' => 'required|integer|min:1|max:10',

            'physical_score' => 'required|integer|min:1|max:10',

            'safety_score' => 'required|integer|min:1|max:10',

            'school_score' => 'required|integer|min:1|max:10',

            'relationship_score' => 'required|integer|min:1|max:10',

            'journal_notes' => 'nullable|string',

        ]);



        WellbeingCheck::create([

            'case_file_id' => $request->case_file_id,

            'overall_score' => $request->overall_score,

            'emotional_score' => $request->emotional_score,

            'behavioural_score' => $request->behavioural_score,

            'physical_score' => $request->physical_score,

            'safety_score' => $request->safety_score,

            'school_score' => $request->school_score,

            'relationship_score' => $request->relationship_score,

            'journal_notes' => $request->journal_notes,

            'submitted_by' => auth()->id(),

        ]);



        return back()->with('success', 'Wellbeing check submitted successfully.');

    }

}

