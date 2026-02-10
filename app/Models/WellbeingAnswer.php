<?php
class WellbeingAnswer extends Model
{
    use HasFactory;

    protected $fillable = ['checkid', 'questionid', 'raw_value', 'normalized_score', 'risk_score', 'tag'];

    public function wellbeingCheck()
    {
        return $this->belongsTo(WellbeingCheck::class, 'checkid');
    }

    public function question()
    {
        return $this->belongsTo(WellbeingQuestion::class, 'questionid');
    }
}
