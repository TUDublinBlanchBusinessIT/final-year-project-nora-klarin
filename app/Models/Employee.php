<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $table = 'employee';

    public function cases()
    {
        return $this->belongsToMany(
            CareCase::class,
            'caseemployee',
            'employeeid',
            'caseid'
        )->withPivot('role');
    }
}
