<?php

namespace App\Http\Controllers;

use App\Models\CaseFile;
use App\Models\Placement;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{

    public function dashboard()
    {
        $stats = [
            'total_users'      => User::count(),
            'total_cases'      => CaseFile::count(),
            'open_cases'       => CaseFile::where('status', 'open')->count(),
            'closed_cases'     => CaseFile::where('status', 'closed')->count(),
            'high_risk_cases'  => CaseFile::where('risk_level', 'high')->count(),
            'total_children'   => User::where('role', 'young_person')->count(),
            'total_carers'     => User::where('role', 'carer')->count(),
            'total_sw'         => User::where('role', 'social_worker')->count(),
        ];
 
        $recentCases = CaseFile::with('youngPerson')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();
 
        return view('admin.dashboard', compact('stats', 'recentCases'));
    }

    public function index()
    {
        $users = User::all();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

        public function editUser(User $user)
    {
        $carers        = User::where('role', 'carer')->orderBy('name')->get(['id', 'name']);
        $socialWorkers = User::where('role', 'social_worker')->orderBy('name')->get(['id', 'name']);
        return view('admin.users.edit', compact('user', 'carers', 'socialWorkers'));
    }
 
    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'nullable|string|max:255|unique:users,username,' . $user->id,
            'email'    => 'nullable|email|max:255|unique:users,email,' . $user->id,
            'role'     => 'required|in:admin,social_worker,carer,young_person',
            'dob'      => 'nullable|date',
            'carer_id' => 'nullable|exists:users,id',
            'password' => 'nullable|string|min:6',
        ]);
 
        if (empty($validated['password'])) {
            unset($validated['password']);
        } else {
            $validated['password'] = Hash::make($validated['password']);
        }
 
        $user->update($validated);
 
        return redirect()->route('admin.users.index')
            ->with('success', "User {$user->name} updated successfully.");
    }
 
    public function destroyUser(User $user)
    {
        $name = $user->name;
        $user->delete();
 
        return redirect()->route('admin.users.index')
            ->with('success', "{$name} has been removed.");
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

       public function cases(Request $request)
    {
        $query = CaseFile::with(['youngPerson', 'socialWorkers', 'carers'])
            ->orderByDesc('created_at');
 
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
 
        if ($request->filled('risk_level')) {
            $query->where('risk_level', $request->risk_level);
        }
 
        $cases = $query->get();
 
        return view('admin.cases.index', compact('cases'));
    }

        public function socialWorkers()
    {
        $socialWorkers = User::where('role', 'social_worker')
            ->withCount(['socialWorkerCases as total_cases',
                'socialWorkerCases as open_cases' => fn ($q) => $q->where('status', 'open'),
                'socialWorkerCases as high_risk_cases' => fn ($q) => $q->where('risk_level', 'high'),
            ])
            ->orderBy('name')
            ->get();
 
        return view('admin.social_workers', compact('socialWorkers'));
    }

        public function placementsMap()
    {
        $placements = Placement::with(['caseFile.youngPerson', 'carer'])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('status', 'active')
            ->get();
 
        return view('admin.placements_map', compact('placements'));
    }
}

