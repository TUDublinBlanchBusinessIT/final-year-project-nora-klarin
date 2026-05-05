<?php

namespace App\Http\Controllers;

use App\Models\CaseFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class CarerCaseFileController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $cases = CaseFile::whereHas('users', function ($q) use ($user) {
            $q->where('case_user.user_id', $user->id)
              ->where('case_user.role', 'carer');
        })->with([
            'youngPerson',
            'placements',
            'appointments',
            'wellbeingChecks',
        ])->where('status', 'open')->get();

        return view('carer.cases.index', compact('cases'));
    }

    public function show(CaseFile $case)
    {
        $user = Auth::user();

        // Ensure carer is assigned to this case
        abort_if(
            !$case->users()->where('users.id', $user->id)->where('case_user.role', 'carer')->exists(),
            403
        );

        $case->load([
            'youngPerson',
            'users',
            'placements',
            'medicalInfos',
            'educationInfos',
            'documents.uploadedBy',
            'appointments',
            'wellbeingChecks.domainScores.domain',
        ]);

        return view('carer.cases.show', compact('case'));
    }

    public function storeDocument(Request $request, CaseFile $case)
    {
        $user = Auth::user();

        abort_if(
            !$case->users()->where('users.id', $user->id)->where('case_user.role', 'carer')->exists(),
            403
        );

        $request->validate([
            'name' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png,docx|max:10240',
        ]);

        $path = $request->file('file')->store('documents', 'public');

        $case->documents()->create([
            'name'        => $request->name,
            'file_path'   => $path,
            'uploaded_by' => $user->id,
        ]);

        // Notify the assigned social worker(s)
        $uploaderName = $user->name;
        \App\Models\User::whereIn('id',
            \Illuminate\Support\Facades\DB::table('case_user')
                ->where('case_file_id', $case->id)
                ->where('role', 'social_worker')
                ->pluck('user_id')
        )->get()->each(fn($sw) => $sw->notify(new \App\Notifications\CareHubNotification(
            type: 'document_uploaded',
            summary: $uploaderName . ' uploaded "' . $request->name . '"',
            data: ['case_file_id' => $case->id],
        )));

        return back()->with('success', 'Document uploaded.');
    }
}