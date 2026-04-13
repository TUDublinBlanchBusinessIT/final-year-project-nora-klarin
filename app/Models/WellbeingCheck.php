<?php



namespace App\Models;



use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

use App\Models\User;

use App\Models\CaseFile;



class WellbeingCheck extends Model

{

    use HasFactory;



    protected $table = 'wellbeing_checks';



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

        'submitted_by',

        'tag_summary',

        'safeguarding_flag',

    ];



    public function caseFile()

    {

        return $this->belongsTo(CaseFile::class, 'case_file_id');

    }



    public function submittedBy()

    {

        return $this->belongsTo(User::class, 'submitted_by');

    }



    public function domainScores()

    {

        return $this->hasMany(DomainScore::class);

    }



    public function responses()

    {

        return $this->hasMany(WellbeingAnswer::class, 'checkid');

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