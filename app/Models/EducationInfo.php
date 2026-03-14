<?php



namespace App\Models;



use Illuminate\Database\Eloquent\Model;



class EducationInfo extends Model

{

    protected $fillable = [

        'case_file_id',

        'school_name',

        'grade',

        'notes',

    ];



    public function caseFile()

    {

        return $this->belongsTo(CaseFile::class, 'case_file_id');

    }

}