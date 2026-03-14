<?php



namespace App\Models;



use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;



class WellbeingCheck extends Model

{

    use HasFactory;



    protected $table = 'wellbeing_checks';



    protected $fillable = [

        'youngpersonid',

        'overallscore',

        'notes',

    ];



    public function caseFile()

    {

        return $this->belongsTo(CaseFile::class, 'caseid');

    }



    public function youngPerson()

    {

        return $this->belongsTo(User::class, 'youngpersonid', 'id');

    }



    public function answers()

    {

        return $this->hasMany(WellbeingAnswer::class, 'checkid');

    }

}

