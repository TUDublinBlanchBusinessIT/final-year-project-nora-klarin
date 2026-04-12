<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class ChatMessage extends Model
{
    /**
     * Mass assignable fields
     */
    protected $fillable = [
        'user_id',
        'user_message',
        'bot_reply',
        'matched_category',
        'is_emergency',
    ];

    /**
     * Type casting
     */
    protected $casts = [
        'is_emergency' => 'boolean',
    ];

    /**
     * Relationship: Chat message belongs to a user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: Only emergency messages
     */
    public function scopeEmergency($query)
    {
        return $query->where('is_emergency', true);
    }

    /**
     * Scope: Filter by category
     */
    public function scopeCategory($query, $category)
    {
        return $query->where('matched_category', $category);
    }

    /**
     * Scope: Latest messages first
     */
    public function scopeLatestFirst($query)
    {
        return $query->orderByDesc('created_at');
    }

    /**
     * Check if message is emergency
     */
    public function isEmergency(): bool
    {
        return $this->is_emergency === true;
    }

    /**
     * Format for chatbot display (optional helper)
     */
    public function toChatArray(): array
    {
        return [
            'user_message' => $this->user_message,
            'bot_reply' => $this->bot_reply,
            'category' => $this->matched_category,
            'is_emergency' => $this->is_emergency,
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}