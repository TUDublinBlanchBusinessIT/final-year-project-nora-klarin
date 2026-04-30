<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DomainScore extends Model
{
    protected $table = 'wellbeing_domain_scores';

    protected $fillable = [
        'wellbeing_check_id',
        'domain_id',
        'average_score',
        'risk_score'
    ];

    protected $casts = [
        'average_score' => 'float',
        'risk_score' => 'float',
    ]; 
    public function domain()
    {
        return $this->belongsTo(Domain::class, 'domain_id');
    }

    public function wellbeingCheck()
    {
        return $this->belongsTo(WellbeingCheck::class, 'wellbeing_check_id');
    }

}
