<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\TrustedPerson;
use App\Models\User;


class TrustedPeopleController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $people = TrustedPerson::where('child_id', $userId)
            ->orderByDesc('id')
            ->get();

        $carer = $this->getAssignedUser($userId, 'carer');
        $socialWorker = $this->getAssignedUser($userId, 'social_worker');

        return view('child.trusted-people', compact('people', 'carer', 'socialWorker'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'relationship' => ['required', 'string', 'max:255'],
            'phone'        => ['nullable', 'string', 'max:50'],
            'email'        => ['nullable', 'email', 'max:255'],
        ]);

        TrustedPerson::create([
            'child_id'     => Auth::id(),
            'name'         => $data['name'],
            'relationship' => $data['relationship'],
            'phone'        => $data['phone'] ?? null,
            'email'        => $data['email'] ?? null,
        ]);

        return back()->with('success', 'Trusted person added successfully.');
    }

    public function requestSupport(Request $request)
    {
        $child = Auth::user();

        $data = $request->validate([
            'message' => ['nullable', 'string', 'max:1000'],
        ]);

        $message = $data['message'] ?? 'I need support.';

        // Resolve assigned carer and social worker from the open case
        $carer        = $this->getAssignedUser($child->id, 'carer');
        $socialWorker = $this->getAssignedUser($child->id, 'social_worker');

        // Persist the support request
        DB::table('support_requests')->insert([
            'user_id'    => $child->id,
            'child_id'   => $child->id,
            'carer_id'   => $carer?->id,
            'status'     => 'open',
            'message'    => $message,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Notify whoever is assigned — notify() handles missing gracefully
        $notification = new CareHubNotification(
            type: 'support_request',
            summary: $child->name . ' has requested support.',
            data: [
                'child_id'     => $child->id,
                'case_file_id' => $caseFile?->id,
                'message'      => $message,
            ],
        );

        if ($socialWorker) {
            User::find($socialWorker->id)?->notify($notification);
        }

        if ($carer) {
            User::find($carer->id)?->notify($notification);
        }

        return back()->with('support_sent', true);
    }

    /**
     * Returns the assigned user of the given role for the child's open case.
     * Returns null if no open case or no user of that role is assigned.
     */
    private function getAssignedUser(int $childId, string $role): ?object
    {
        return DB::table('case_user')
            ->join('case_files', 'case_user.case_file_id', '=', 'case_files.id')
            ->join('users', 'case_user.user_id', '=', 'users.id')
            ->where('case_files.young_person_id', $childId)
            ->where('case_files.status', 'open')
            ->where('case_user.role', $role)
            ->select('users.id', 'users.name', 'users.email')
            ->first();
    }
}
