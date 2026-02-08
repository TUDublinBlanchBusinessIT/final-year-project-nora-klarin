<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\CaseFile;

class SocialWorkerDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        abort_if($user->role !== 'social_worker', 403);

        $cases = CaseFile::whereHas('users', function ($query) use ($user) {
            $query->where('users.id', $user->id)
                  ->where('caseemployee.role', 'social_worker');
        })->get();

        return view('socialworker.dashboard', compact('cases'));
    }
}
