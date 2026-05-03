<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WellbeingAnswer extends Model
{
    use HasFactory;

    protected $table = 'wellbeing_responses';

    protected $fillable = [
        'wellbeing_check_id',
        'question_id',
        'raw_value',
        'normalised_score',
        'risk_contribution',
        'respondent_type',
    ];

    public function wellbeingCheck()
    {
        return $this->belongsTo(WellbeingCheck::class, 'wellbeing_check_id');
    }

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }
}