<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Services\ChatbotService;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index()
    {
        // Load last 6 exchanges for context display
        $history = ChatMessage::where('user_id', auth()->id())
            ->latest()
            ->take(6)
            ->get()
            ->reverse()
            ->values();

        return view('chatbot', compact('history'));
    }

    public function send(Request $request, ChatbotService $chatbotService)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'history' => 'nullable|array',
            'history.*.user' => 'string',
            'history.*.bot'  => 'string',
        ]);
            try {
        $result = $chatbotService->getReply(
            $request->message,
            $request->input('history', [])
        );
    } catch (\Throwable $e) {
        \Illuminate\Support\Facades\Log::error('Chatbot error: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString(),
        ]);
        return response()->json([
            'reply'        => "I'm having a little trouble right now — please try again in a moment.",
            'category'     => null,
            'is_emergency' => false,
            'suggestions'  => ['Housing Help', 'Wellbeing', 'Talk to Someone', 'Emergency Help'],
            'link_label'   => null,
            'link_url'     => null,
        ]);
    }
        ChatMessage::create([
            'user_id'          => auth()->id(),
            'user_message'     => $request->message,
            'bot_reply'        => $result['reply'],
            'matched_category' => $result['category'],
            'is_emergency'     => $result['is_emergency'],
        ]);

        return response()->json($result);
    }
}