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

        $caseFile = CaseFile::where('young_person_id', $child->id)->first();

        if (!$caseFile) {
            abort(404, 'No case file found for this child.');
        }

        $carerLink = DB::table('case_user')
            ->where('case_id', $caseFile->id)
            ->where('role', 'carer')
            ->first();

        if (!$carerLink) {
            abort(404, 'No carer linked to this case.');
        }

        $carer = User::find($carerLink->user_id);

        if (!$carer) {
            abort(404, 'Linked carer not found.');
        }

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
            'caseFile' => $caseFile,
        ]);
    }

    public function store(Request $request, Thread $thread)
    {
        $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $userId = Auth::id();

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