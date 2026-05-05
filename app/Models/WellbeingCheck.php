<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WellbeingCheck extends Model
{
    use HasFactory;

    protected $table = 'wellbeing_checks';

    protected $fillable = [
        'young_person_id',
        'case_file_id',
        // overall_score is the single wellbeing score column in the schema.
        // overall_risk_score is not stored here — it is derived on demand
        // by summing wellbeing_domain_scores.risk_score for this check.
        'overall_score',
        'completed_at',
        'check_type',
        'game_mode',
        'submitted_by',
        'risk_level',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];

    public function caseFile()
    {
        return $this->belongsTo(CaseFile::class, 'case_file_id');
    }

    public function youngPerson()
    {
        return $this->belongsTo(User::class, 'young_person_id');
    }

    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function domainScores()
    {
        return $this->hasMany(DomainScore::class, 'wellbeing_check_id');
    }

    public function responses()
    {
        return $this->hasMany(WellbeingAnswer::class, 'wellbeing_check_id');
    }

    public function alerts()
    {
        return $this->hasMany(Alert::class, 'wellbeing_check_id');
    }
}
