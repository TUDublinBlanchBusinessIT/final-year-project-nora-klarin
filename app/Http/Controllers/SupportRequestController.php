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

        DB::table('support_requests')->insert([
            'user_id' => $child->id,      // REQUIRED (fixes your error)
            'child_id' => $child->id,
            'carer_id' => $child->carer_id,
            'status' => 'open',
            'message' => null,            // no message box anymore
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with(
            'success',
            '✅ Support alert sent. Your carer has been notified.'
        );
    }
}