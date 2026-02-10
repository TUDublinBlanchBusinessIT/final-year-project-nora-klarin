<?php
class WellbeingQuestion extends Model
{
    use HasFactory;

    protected $fillable = ['text', 'domain', 'tags', 'age_band', 'min_value', 'max_value', 'positive_frame'];

    public function responses()
    {
        return $this->hasMany(WellbeingResponse::class, 'questionid');
    }
}
