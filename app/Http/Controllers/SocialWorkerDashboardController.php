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

        $cases = $user->socialWorkerCases()->get();

        return view('socialworker.dashboard', compact('cases'));
    }
}
