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
public function index(Request $request)
{
    $child = Auth::user();

    if ($child->role !== 'young_person') {
        abort(403);
    }

    $withId = (int) $request->query('with', 0);

    // People I've ever messaged or been messaged by
    $partnerIds = Message::query()
        ->where('sender_id', $child->id)
        ->orWhere('recipient_id', $child->id)
        ->get(['sender_id', 'recipient_id'])
        ->flatMap(fn ($m) => [$m->sender_id, $m->recipient_id])
        ->unique()
        ->reject(fn ($id) => $id == $child->id)
        ->values();

    $conversations = User::query()
        ->whereIn('id', $partnerIds)
        ->orderBy('name')
        ->get(['id', 'name', 'email', 'role'])
        ->map(function ($partner) use ($child) {
            // Last message between me and this partner
            $last = Message::query()
                ->where(function ($q) use ($child, $partner) {
                    $q->where('sender_id', $child->id)->where('recipient_id', $partner->id);
                })
                ->orWhere(function ($q) use ($child, $partner) {
                    $q->where('sender_id', $partner->id)->where('recipient_id', $child->id);
                })
                ->latest('created_at')
                ->first(['body', 'created_at', 'sender_id', 'read_at']);

            // Unread messages from partner -> me
            $unreadCount = Message::query()
                ->where('sender_id', $partner->id)
                ->where('recipient_id', $child->id)
                ->whereNull('read_at')
                ->count();

            $partner->last_body = $last?->body;
            $partner->last_at = $last?->created_at;
            $partner->unread_count = $unreadCount;

            return $partner;
        })
        // Sort by latest message time (so it feels like a real inbox)
        ->sortByDesc(fn ($p) => $p->last_at ?? now()->subYears(50))
        ->values();

    // If none selected, default to first conversation
    if ($withId === 0 && $conversations->count() > 0) {
        $withId = $conversations->first()->id;
    }

    $selectedUser = $withId ? User::find($withId) : null;

    $messages = collect();

    if ($selectedUser) {
        // Pull messages between me and selected user
        $messages = Message::query()
            ->with(['sender:id,name', 'recipient:id,name'])
            ->where(function ($q) use ($child, $withId) {
                $q->where('sender_id', $child->id)
                  ->where('recipient_id', $withId);
            })
            ->orWhere(function ($q) use ($child, $withId) {
                $q->where('sender_id', $withId)
                  ->where('recipient_id', $child->id);
            })
            ->orderBy('created_at')
            ->get();

        // Mark received messages as read
        Message::query()
            ->where('sender_id', $withId)
            ->where('recipient_id', $child->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    return view('child.messages.index', compact('conversations', 'selectedUser', 'messages'));
}

public function store(Request $request)
{
    $child = Auth::user();

    abort_unless($child->role === 'young_person', 403);

    $data = $request->validate([
        'recipient_id' => ['required', 'exists:users,id'],
        'body' => ['required', 'string', 'max:2000'],
    ]);

    $recipient = User::findOrFail($data['recipient_id']);

    if (!User::canMessage($child, $recipient)) {
        abort(403, 'Not allowed to message this user.');
    }

    Message::create([
        'sender_id' => $child->id,
        'recipient_id' => $recipient->id,
        'body' => $data['body'],
    ]);

    return redirect()
        ->route('child.messages.index', ['with' => $recipient->id])
        ->with('status', 'Message sent!');
}
}