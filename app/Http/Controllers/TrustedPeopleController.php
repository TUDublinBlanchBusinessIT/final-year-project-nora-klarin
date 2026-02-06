<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TrustedPeopleController extends Controller
{
    /**
     * Show trusted people page
     */
    public function index()
    {
        $people = DB::table('trusted_people')
            ->where('user_id', Auth::id())
            ->get();

        return view('child.trusted-people', [
            'people' => $people
        ]);
    }

    /**
     * Save a trusted person
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'relationship' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
        ]);

        DB::table('trusted_people')->insert([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'relationship' => $request->relationship,
            'phone' => $request->phone,
            'email' => $request->email,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Trusted person added successfully.');
    }
}
