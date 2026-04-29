<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Appointment extends Model
{
    protected $table = 'appointments';

    protected $fillable = [
        'user_id',
        'title',
        'date',
        'time',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'appointment_user',
            'appointment_id',
            'user_id'
        );
    }
}