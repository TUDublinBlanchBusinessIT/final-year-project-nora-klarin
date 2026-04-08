<?php

namespace App\Http\Controllers;

use App\Models\DiaryEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DiaryEntryController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'   => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'mood'    => ['nullable', 'in:happy,calm,okay,worried,sad'],
            'private' => ['nullable'],
        ]);

        DiaryEntry::create([
            'user_id' => Auth::id(),
            'title'   => $data['title'],
            'content' => $data['content'] ?? null,
            'mood'    => $data['mood'] ?? null,
            'private' => $request->boolean('private'),
        ]);

        return back()->with('success', 'Diary entry saved!');
    }
}
