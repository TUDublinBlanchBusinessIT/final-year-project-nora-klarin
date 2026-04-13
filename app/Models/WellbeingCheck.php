<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WellbeingCheck extends Model
{
    protected $table = 'wellbeing_checks'; 
    protected $fillable = [
        'young_person_id',
        'case_file_id',
        'overall_score',
        'completed_at',
        'check_type',
        'game_mode',
    ];
protected $casts = ['completed_at' => 'datetime'];
    public function caseFile()
    {
        return $this->belongsTo(CaseFile::class);
    }

    public function domainScores()
    {
        return $this->hasMany(DomainScore::class);
    }

    public function responses()
    {
        return $this->hasMany(WellbeingAnswer::class, 'wellbeing_check_id');
    }

    public function alerts()
    {
        return $this->hasMany(Alert::class, 'wellbeing_check_id');
    }

    public function getRiskLevelAttribute()
    {
        $score = $this->overall_score;

        if ($score >= 70) {
            return 'low';
        } elseif ($score >= 50) {
            return 'medium';
        } elseif ($score >= 30) {
            return 'high';
        } else {
            return 'critical';
        }
    }

}
