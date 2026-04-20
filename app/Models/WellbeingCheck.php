<?php



namespace App\Models;



use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

use App\Models\User;

use App\Models\CaseFile;



class WellbeingCheck extends Model

{
    protected $fillable = [
        'child_id',
        'completed_by_type',
        'completed_by_user_id',
        'week_start',
        'overall_score',
        'overall_risk_score',
        'risk_level'
    ];

    public function responses()
    {
        return $this->hasMany(WellbeingResponse::class);
    }

    public function domainScores()
    {
    return $this->hasMany(WellbeingDomainScore::class);
    }



    public function getRiskLevelAttribute()

    {

        $score = $this->overall_score ?? 0;



        if ($score >= 70) {

            return 'low';

        } elseif ($score >= 50) {

            return 'medium';

        } elseif ($score >= 30) {

            return 'high';

        }

        return 'critical';

    }

}