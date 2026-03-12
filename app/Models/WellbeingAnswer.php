<?php
class WellbeingAnswer extends Model
{
    use HasFactory;

    protected $fillable = ['wellbeing_check_id', 'question_id', 'raw_value', 'normalized_score', 'risk_score', 'tag'];

    public function wellbeingCheck()
    {
        return $this->belongsTo(WellbeingCheck::class, 'wellbeing_check_id');
    }

    public function question()
    {
        return $this->belongsTo(WellbeingQuestion::class, 'question_id');
    }
}
