<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class CarerMessageController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if (($user->role ?? null) !== 'carer') {
            abort(403);
        }

        $withId = (int) $request->query('with', 0);

        // People I've ever messaged or been messaged by
        $partnerIds = Message::query()
            ->where('sender_id', $user->id)
            ->orWhere('recipient_id', $user->id)
            ->get(['sender_id', 'recipient_id'])
            ->flatMap(fn ($m) => [$m->sender_id, $m->recipient_id])
            ->unique()
            ->reject(fn ($id) => $id == $user->id)
            ->values();

        $conversations = User::query()
            ->whereIn('id', $partnerIds)
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'role'])
            ->map(function ($partner) use ($user) {
                // Last message between me and this partner
                $last = Message::query()
                    ->where(function ($q) use ($user, $partner) {
                        $q->where('sender_id', $user->id)->where('recipient_id', $partner->id);
                    })
                    ->orWhere(function ($q) use ($user, $partner) {
                        $q->where('sender_id', $partner->id)->where('recipient_id', $user->id);
                    })
                    ->latest('created_at')
                    ->first(['body', 'created_at', 'sender_id', 'read_at']);

                // Unread messages from partner -> me
                $unreadCount = Message::query()
                    ->where('sender_id', $partner->id)
                    ->where('recipient_id', $user->id)
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
                ->where(function ($q) use ($user, $withId) {
                    $q->where('sender_id', $user->id)
                      ->where('recipient_id', $withId);
                })
                ->orWhere(function ($q) use ($user, $withId) {
                    $q->where('sender_id', $withId)
                      ->where('recipient_id', $user->id);
                })
                ->orderBy('created_at')
                ->get();

            // Mark received messages as read
            Message::query()
                ->where('sender_id', $withId)
                ->where('recipient_id', $user->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }

        return view('carer.messages.index', compact('conversations', 'selectedUser', 'messages'));
    }

    public function create(Request $request)
    {
        $user = $request->user();

        if (($user->role ?? null) !== 'carer') {
            abort(403);
        }

        $recipients = User::query()
            ->where('id', '!=', $user->id)
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'role']);

        return view('carer.messages.create', compact('recipients'));
    }

    public function store(Request $request)
    {
        $user = $request->user();

        if (($user->role ?? null) !== 'carer') {
            abort(403);
        }

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
            ->route('carer.messages.index', ['with' => $data['recipient_id']])
            ->with('status', 'Message sent!');
    }
}
