<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WellbeingCheck extends Model
{
    protected $fillable = [
        'child_id',
        'completed_by_type',
        'completed_by_user_id',
        'week_start',
        'overall_score',
        'overall_risk_score',
        'risk_level'
    ];

    public function responses()
    {
        return $this->hasMany(WellbeingResponse::class);
    }

    public function domainScores()
    {
    return $this->hasMany(WellbeingDomainScore::class);
    }
}
