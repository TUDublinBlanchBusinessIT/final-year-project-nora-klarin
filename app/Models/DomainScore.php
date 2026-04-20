<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DomainScore extends Model
{
    protected $table = 'wellbeing_domain_scores'; // <-- use your actual table

    protected $fillable = [
        'wellbeing_check_id',
        'domain_id',
        'average_score',
        'risk_score'
    ];

    public function domain()
    {
        return $this->belongsTo(Domain::class);
    }

    public function wellbeingCheck()
    {
        return $this->belongsTo(WellbeingCheck::class);
    }
}
