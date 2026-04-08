<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SupportRequestController extends Controller
{
    public function index()
    {
        // Show the page
        return view('child.support');
    }

    public function store(Request $request)
    {
        // Create a support request row (DB proof that it works)
        DB::table('support_requests')->insert([
            'user_id' => Auth::id(),
            'status' => 'open',
            'message' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', '✅ Support request sent. A trusted adult will be alerted soon (later feature).');
    }
}
