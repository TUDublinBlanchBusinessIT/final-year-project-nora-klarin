<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class YoungPerson extends Model
{
    protected $table = 'youngperson'; // match your table name
    public $timestamps = false;        // your table uses `createdat`, not Laravel timestamps

    protected $fillable = [
        'firstname',
        'secondname',
        'dob',
        'passwordhash',
        'createdat',
    ];

    // Cases for this young person
    public function cases()
    {
        return $this->hasMany(CaseFile::class, 'youngpersonid');
    }
}

