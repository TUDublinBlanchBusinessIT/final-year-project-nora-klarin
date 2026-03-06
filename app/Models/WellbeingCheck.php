<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WellbeingCheck extends Model
{
    use HasFactory;

    protected $table = 'wellbeingcheck';
    protected $fillable = ['youngpersonid', 'overallscore', 'notes'];

    public function caseFile()
    {
        return $this->belongsTo(CaseFile::class, 'caseid');
    }

    public function youngPerson()
    {
        return $this->belongsTo(YoungPerson::class, 'youngpersonid');
    }

    
    public function answers()
    {
        return $this->hasMany(WellbeingAnswer::class, 'checkid');
    }
}
