<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WellbeingCheck extends Model
{
    protected $table = 'wellbeing_checks'; // <- REQUIRED if Laravel guesses wrong
    protected $fillable = [
        'case_file_id',
        'overall_score',
        'emotional_score',
        'behavioural_score',
        'physical_score',
        'safety_score',
        'school_score',
        'relationship_score',
        'journal_notes',
        'tag_summary',
        'safeguarding_flag'
    ];

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
        return $this->hasMany(WellbeingAnswer::class, 'checkid');
    }

}
