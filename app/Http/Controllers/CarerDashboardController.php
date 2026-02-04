<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CarerDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // simple safety check
        if ($user->role !== 'carer') {
            abort(403);
        }

        return view('carer.dashboard', compact('user'));
    }
}
