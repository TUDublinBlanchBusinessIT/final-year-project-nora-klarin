<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CaseFile extends Model
{
    protected $table = 'case';

    protected $fillable = ['youngpersonid', 'risklevel', 'openedat', 'status'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'case_user', 'case_id', 'user_id')
                    ->withPivot('role', 'assigned_at');
    }

    public function socialWorkers()
    {
        return $this->users()->wherePivot('role', 'social_worker');
    }

    public function carers()
    {
        return $this->users()->wherePivot('role', 'carer');
    }
}

