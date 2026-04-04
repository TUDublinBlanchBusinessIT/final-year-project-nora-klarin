<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CaseFile;

class CarerCaseFileController extends Controller
{
    public function show(Request $request, $case)
    {
        $user = $request->user();

        if (($user->role ?? null) !== 'carer') {
            abort(403, 'Unauthorized');
        }

        $case = CaseFile::with([
            'youngPerson',
            'carers',
            'placements.placement.carer',
            'medicalInfos',
            'educationInfos',
            'documents',
            'appointments.creator',
            'appointments.carers',
            'wellbeingChecks',
        ])
        ->where(function ($query) use ($case) {
            $query->where('id', $case)
                  ->orWhere('case_code', $case);
        })
        ->whereHas('carers', function ($query) use ($user) {
            $query->where('users.id', $user->id);
        })
        ->firstOrFail();

        return view('carer.case-file.show', [
            'case' => $case,
        ]);
    }
}