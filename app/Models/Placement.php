<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Placement extends Model
{
    use HasFactory;

    protected $table = 'placement';
    protected $fillable = ['caseid', 'carerid', 'placementtype', 'startdate', 'enddate'];

    public function caseFile()
    {
        return $this->belongsTo(CaseFile::class, 'caseid');
    }

    public function carer()
    {
        return $this->belongsTo(User::class, 'carerid');
    }
}
