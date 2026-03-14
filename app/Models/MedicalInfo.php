<?php



namespace App\Models;



use Illuminate\Database\Eloquent\Model;



class MedicalInfo extends Model

{

    protected $fillable = [

        'case_file_id',

        'condition',

        'notes',

    ];



    public function caseFile()

    {

        return $this->belongsTo(CaseFile::class, 'case_file_id');

    }

}