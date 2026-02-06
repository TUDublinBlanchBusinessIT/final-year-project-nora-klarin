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

        $messages = Message::query()
            ->where('recipient_id', $user->id)
            ->latest()
            ->paginate(10);

        return view('carer.messages.index', compact('messages'));
    }

    public function create(Request $request)
    {
        $user = $request->user();

        if (($user->role ?? null) !== 'carer') {
            abort(403);
        }

        // For now: carer can message anyone (we’ll restrict later)
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

        return redirect()->route('carer.messages.index')->with('status', 'Message sent!');
    }
}

