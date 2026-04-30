<?php

namespace App\Http\Controllers;

use App\Models\CaseFile;
use App\Models\Message;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChildMessageController extends Controller
{
public function index()
{
    $child = Auth::user();

    $carers = User::where('role', 'carer')
        ->where('id', $child->carer_id)
        ->get();

    $socialWorkers = User::where('role', 'social_worker')
        ->whereHas('socialWorkerCases', function ($q) use ($child) {
            $q->where('young_person_id', $child->id);
        })
        ->get();

    $contacts = $carers->merge($socialWorkers)->unique('id');

    $threads = collect();
    $messages = collect();
    $activeThread = null;

    if ($contacts->isNotEmpty()) {

        $firstContact = $contacts->first();

        $activeThread = Thread::firstOrCreate([
            'child_id' => $child->id,
            'recipient_id' => $firstContact->id,
        ]);

        $messages = $activeThread->messages()
            ->with('sender')
            ->orderBy('created_at')
            ->get();
    
        $threads = Thread::firstOrCreate([
    'child_id' => $child->id,
    'carer_id' => $firstContact?->id]);
}
    return view('child.messages', [
        'contacts' => $contacts,
        'threads' => $threads,
        'messages' => $messages,
        'activeThread' => $activeThread,
    ]);
}

public function store(Request $request, Thread $threads = null)
{
    $request->validate([
        'body' => ['required', 'string', 'max:2000'],
        'recipient_id' => ['required', 'exists:users,id'],
    ]);

    $child = Auth::user();

        $data = $request->validate([
            'recipient_id' => ['required', 'exists:users,id'],
            'body' => ['required', 'string', 'max:2000'],
        ]);

        Message::create([
            'sender_id' => $user->id,
            'recipient_id' => $data['recipient_id'],
            'body' => $data['body'],
        ]);

        return redirect()
            ->route('child.messages.index', ['with' => $data['recipient_id']])
            ->with('status', 'Message sent!');
    }
}