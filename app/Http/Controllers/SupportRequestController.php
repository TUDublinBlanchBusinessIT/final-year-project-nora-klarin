<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class SupportRequestController extends Controller
{
    public function index()
    {
        $child = Auth::user();

        // Get linked carer
        $carer = null;
        if (!empty($child->carer_id)) {
            $carer = User::find($child->carer_id);
        }

        return view('child.support', [
            'carer' => $carer,
        ]);
    }

    public function store(Request $request)
    {
        $child = Auth::user();

        // Create support request linked to BOTH child + carer
        DB::table('support_requests')->insert([
            'child_id' => $child->id,
            'carer_id' => $child->carer_id,
            'status' => 'open',
            'message' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with(
            'success',
            '✅ Support request sent. Your carer has been notified.'
        );
    }
}