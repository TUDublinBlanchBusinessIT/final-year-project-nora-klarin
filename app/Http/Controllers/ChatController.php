<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Services\ChatbotService;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index()
    {
        return view('chatbot');
    }

    public function send(Request $request, ChatbotService $chatbotService)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $result = $chatbotService->getReply($request->message);

        ChatMessage::create([
            'user_id' => auth()->id(),
            'user_message' => $request->message,
            'bot_reply' => $result['reply'],
            'matched_category' => $result['category'],
            'is_emergency' => $result['is_emergency'],
        ]);

        return response()->json($result);
    }
}