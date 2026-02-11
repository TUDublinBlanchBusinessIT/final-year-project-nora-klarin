<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChildMessageController extends Controller
{
    public function index()
    {
        $child = Auth::user();

        // ✅ Use the child’s assigned carer_id (NOT "first carer in DB")
        $carerId = $child->carer_id;

        abort_unless($carerId, 404, 'No carer assigned to this child.');

        $thread = Thread::firstOrCreate([
            'child_id' => $child->id,
            'carer_id' => $carerId,
        ]);

        $messages = $thread->messages()
            ->with('sender')
            ->orderBy('created_at')
            ->get();

        $carer = User::find($carerId);

        return view('child.messages', compact('thread', 'messages', 'carer'));
    }

    public function store(Request $request, Thread $thread)
    {
        $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        // ✅ Security: child must own this thread
        abort_unless($thread->child_id === Auth::id(), 403);

        Message::create([
            'thread_id' => $thread->id,
            'sender_id' => Auth::id(),
            'body' => $request->body,
        ]);

        // ✅ IMPORTANT: redirect back to SAME thread chat screen
        return redirect()->route('child.messages.index')->with('success', 'Message sent!');
    }
}
