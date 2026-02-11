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
    return $this->belongsToMany(
        CaseFile::class,   
        'case_user',       
        'user_id',         
        'case_id'         
    )
    ->withPivot('role', 'assigned_at')
    ->wherePivot('role', 'social_worker');
}
public function carerCases()
{
    return $this->belongsToMany(CaseFile::class, 'case_user', 'user_id', 'case_id')
        ->wherePivot('role', 'carer')
        ->withTimestamps();
}

    public function createdAppointments()
    {
        return $this->hasMany(Appointment::class, 'created_by');
    }

    public function appointmentsAsYoungPerson()
    {
        return $this->hasMany(Appointment::class, 'young_person_id');
    }

    public function appointments()
    {
        return $this->belongsToMany(Appointment::class);
    }



}



