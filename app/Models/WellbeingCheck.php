<?php



namespace App\Models;



use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;



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

    ];



    public function caseFile()

    {

        return $this->belongsTo(CaseFile::class, 'case_file_id');

    }



    public function submittedBy()

    {

        return $this->belongsTo(User::class, 'submitted_by');

    }

}