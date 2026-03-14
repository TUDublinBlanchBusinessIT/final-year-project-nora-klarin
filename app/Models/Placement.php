<?php



namespace App\Models;



use Illuminate\Database\Eloquent\Model;



class Placement extends Model

{

    protected $fillable = [

        'case_file_id',

        'carer_id',

        'type',

        'address',

        'notes',

        'start_date',

        'end_date'

    ];



    public function caseFile()

    {

        return $this->belongsTo(CaseFile::class);

    }



    public function carer()

    {

        return $this->belongsTo(User::class, 'carer_id');

    }

}