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

        // child must have an assigned carer
        if (empty($child->carer_id)) {
            abort(404, 'No carer assigned to this child.');
        }

        $carer = User::find($child->carer_id);
        if (!$carer) {
            abort(404, 'Assigned carer not found.');
        }

        // Create or fetch the thread for this child+carer
        $thread = Thread::firstOrCreate([
            'child_id' => $child->id,
            'carer_id' => $carer->id,
        ]);

        $messages = $thread->messages()
            ->orderBy('created_at', 'asc')
            ->get();

        return view('child.messages', [
            'thread' => $thread,
            'messages' => $messages,
            'carer' => $carer,
        ]);
    }

    public function store(Request $request, Thread $thread)
    {
        $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $userId = Auth::id();

        // ✅ Authorization: only the child or the carer on this thread can post
        $allowed = ($thread->child_id === $userId) || ($thread->carer_id === $userId);
        abort_unless($allowed, 403);

        Message::create([
            'thread_id' => $thread->id,
            'sender_id' => $userId,
            'body' => $request->body,
        ]);

        return redirect()->route('child.messages.index');
    }
}