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
        // respondent_type is ENUM('young_person', 'carer') in the schema.
        // WellbeingCheckProcessor maps all roles to one of these two values
        // before writing, so social_worker submissions are stored as 'carer'.
        'respondent_type',
        // tags_fired is intentionally absent — the column does not exist in
        // the wellbeing_responses schema. Fired tag state is recorded in the
        // alerts table via WellbeingAlertService after check completion.
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