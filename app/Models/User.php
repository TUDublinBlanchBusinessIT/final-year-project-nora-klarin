<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role', 'username'];

    public function cases()
    {
        return $this->belongsToMany(CaseFile::class, 'case_user', 'user_id', 'case_file_id')
                    ->withPivot('role', 'assigned_at');
    }

    public function socialWorkerCases()
{
    return $this->belongsToMany(
        CaseFile::class,   
        'case_user',       
        'user_id',         
        'case_file_id'         
    )
    ->withPivot('role', 'assigned_at')
    ->wherePivot('role', 'social_worker');
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



