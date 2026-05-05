<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\TrustedPerson;
use App\Models\User;
use App\Notifications\CareHubNotification;

class TrustedPeopleController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $people = TrustedPerson::where('child_id', $userId)
            ->orderByDesc('id')
            ->get();

        $carer        = $this->getAssignedUser($userId, 'carer');
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

        $carer        = $this->getAssignedUser($child->id, 'carer');
        $socialWorker = $this->getAssignedUser($child->id, 'social_worker');

        // Resolve the open case file
        $caseFile = DB::table('case_files')
            ->where('young_person_id', $child->id)
            ->where('status', 'open')
            ->first();

        $caseFileId = $caseFile?->id;

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

        // ── Create an Alert row so it appears in the SW alert panel ──────────
        // This creates a high-severity alert that shows in the red alert dropdown
        // on the SW dashboard, not buried in the notification bell.
        if ($caseFileId) {
            // We need a wellbeing_check_id for the alerts FK — use the most recent check
            $latestCheckId = DB::table('wellbeing_checks')
                ->where('case_file_id', $caseFileId)
                ->orderByDesc('created_at')
                ->value('id');

            if ($latestCheckId) {
                DB::table('alerts')->insert([
                    'wellbeing_check_id' => $latestCheckId,
                    'young_person_id'    => $child->id,
                    'alert_type'         => 'tag_override',
                    'severity'           => 'high',
                    'message'            => $child->name . ' has requested support: "' . $message . '"',
                    'acknowledged_at'    => null,
                    'acknowledged_by'    => null,
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ]);
            }
        }

        // ── Also send a notification to carer (bell) ──────────────────────────
        // SW sees it in the alert panel; carer gets a notification bell item
        $notification = new CareHubNotification(
            type:    'support_request',
            summary: $child->name . ' has requested support.',
            data:    [
                'child_id'     => $child->id,
                'case_file_id' => $caseFileId,
                'message'      => $message,
            ],
        );

        if ($carer) {
            User::find($carer->id)?->notify($notification);
        }

        if ($socialWorker && !$caseFileId) {
            User::find($socialWorker->id)?->notify($notification);
        }

        return back()->with('support_sent', true);
    }

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
