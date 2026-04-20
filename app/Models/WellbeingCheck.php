<?php



namespace App\Models;



use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\User;

use App\Models\CaseFile;



class WellbeingCheck extends Model

{
    protected $table = 'wellbeing_checks'; 
    protected $fillable = [
        'young_person_id',
        'case_file_id',
        'overall_score',
        'completed_at',
        'check_type',
        'game_mode',
    ];
protected $casts = ['completed_at' => 'datetime'];
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
    return $this->hasMany(WellbeingDomainScore::class);

        return $this->hasMany(DomainScore::class);

    }


    public function responses()

    {
        return $this->hasMany(WellbeingAnswer::class, 'wellbeing_check_id');
    }

    public function alerts()
    {
        return $this->hasMany(Alert::class, 'wellbeing_check_id');
    }

        $score = $this->overall_score ?? 0;

        if ($score >= 70) {

            return 'low';

        } elseif ($score >= 50) {

            return 'medium';

        } elseif ($score >= 30) {

            return 'high';

        }

        return 'critical';

     public function getRiskLevelAttribute(): string
    {
        return match(true) {
            ($this->overall_risk_score ?? 0) >= 250 => 'critical',
            ($this->overall_risk_score ?? 0) >= 150 => 'high',
            ($this->overall_risk_score ?? 0) >= 80  => 'moderate',
            default                                  => 'low',
        };
    }

}