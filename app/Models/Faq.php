<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    /**
     * The table associated with the model (optional, Laravel will auto-detect).
     */
    protected $table = 'faqs';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'question',
        'answer',
        'category',
        'keywords',
        'priority',
        'is_emergency',
        'link_label',
        'link_url',
    ];

    /**
     * Attribute casting.
     */
    protected $casts = [
        'is_emergency' => 'boolean',
        'priority' => 'integer',
    ];

    /**
     * Default attribute values.
     */
    protected $attributes = [
        'priority' => 0,
        'is_emergency' => false,
    ];

    /**
     * Scope: Get FAQs by category
     */
    public function scopeCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope: Order by priority (high → low)
     */
    public function scopePriority($query)
    {
        return $query->orderByDesc('priority');
    }

    /**
     * Helper: Get keywords as array
     */
    public function getKeywordListAttribute(): array
    {
        if (!$this->keywords) {
            return [];
        }

        return array_map('trim', explode(',', strtolower($this->keywords)));
    }

    /**
     * Helper: Check if FAQ has a link
     */
    public function hasLink(): bool
    {
        return !empty($this->link_url);
    }
}