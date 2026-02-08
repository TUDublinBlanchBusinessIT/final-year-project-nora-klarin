<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role', 'username'];

    public function cases()
    {
        return $this->belongsToMany(CaseFile::class, 'case_user', 'user_id', 'case_id')
                    ->withPivot('role', 'assigned_at');
    }

    public function socialWorkerCases()
    {
        return $this->cases()->wherePivot('role', 'social_worker');
    }
}



