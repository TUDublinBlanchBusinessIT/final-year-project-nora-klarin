<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'username' => 'required|string|unique:users,username',
        'email' => 'required|string|unique:users,email',
        'role' => 'required|in:admin,social_worker,carer,young_person',
        'password' => 'nullable|string|min:6',
    ]);

    // Generate temp password if none provided
    $password = $request->password ?? substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'), 0, 8);

    $user = User::create([
        'name' => $request->name,
        'username' => $request->username,
        'email' => $request->email,
        'role' => $request->role,
        'password' => Hash::make($password),
    ]);

    return redirect()->route('admin.users.index')
                     ->with('success', "User created successfully. Temp password: $password");
}



}

