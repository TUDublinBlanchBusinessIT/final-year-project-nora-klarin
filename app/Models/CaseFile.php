<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CaseFile extends Model
{
    protected $table = 'case';

    public function users()
    {
        return $this->belongsToMany(
            \App\Models\User::class,
            'caseemployee',
            'caseid',
            'employeeid'
        );
    }
}
