<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    protected $fillable = [
        'wellbeing_check_id',
        'young_person_id',
        'response_id',
        'tag_id',
        'domain_id',
        'alert_type',
        'severity',
        'message',
        'acknowledged_at',
        'acknowledged_by',
    ];

    public function wellbeingCheck()
    {
        return $this->belongsTo(WellbeingCheck::class);
    }

    public function response()
    {
        return $this->belongsTo(WellbeingResponse::class, 'response_id');
    }

    public function tag()
    {
        return $this->belongsTo(Tag::class);
    }

    public function domain()
    {
        return $this->belongsTo(Domain::class);
    }

    public function acknowledgedBy()
    {
        return $this->belongsTo(User::class, 'acknowledged_by');
    }
}