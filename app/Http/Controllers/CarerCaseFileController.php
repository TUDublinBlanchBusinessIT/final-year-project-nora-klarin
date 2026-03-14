<?php



namespace App\Http\Controllers;



use Illuminate\Http\Request;

use App\Models\CaseFile;



class CarerCaseFileController extends Controller

{

    public function show(Request $request, $id)

    {

        $user = $request->user();



        if (($user->role ?? null) !== 'carer') {

            abort(403);

        }



        $case = CaseFile::with([

            'youngPerson',

            'carers',

            'placements.carer',

            'medicalInfos',

            'educationInfos',

            'documents',

            'appointments.creator',

            'appointments.carers',

            'wellbeingChecks',

        ])

        ->where('id', $id)

        ->firstOrFail();



        return view('carer.case-file.show', [

            'case' => $case,

        ]);

    }

}

