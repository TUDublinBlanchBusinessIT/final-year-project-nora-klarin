<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\CaseFile;

class SocialWorkerDashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $totalCases = CaseFile::where('social_worker_id', $userId)->count();

        $highRiskCases = CaseFile::where('social_worker_id', $userId)
            ->where('risk_level', 'High')
            ->count();

        $openCases = CaseFile::where('social_worker_id', $userId)
            ->where('status', 'Open')
            ->count();

        $recentCases = CaseFile::where('social_worker_id', $userId)
            ->latest()
            ->take(5)
            ->get();

        return view('socialworker.dashboard', compact(
            'totalCases',
            'highRiskCases',
            'openCases',
            'recentCases'
        ));
    }
}
