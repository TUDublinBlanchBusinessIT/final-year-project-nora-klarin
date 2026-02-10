<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\CaseFile;
use App\Models\Appointment;

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

}
