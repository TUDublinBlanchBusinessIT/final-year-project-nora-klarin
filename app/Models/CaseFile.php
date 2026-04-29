<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CaseFile extends Model
{
    protected $table = 'case_files';

    protected $fillable = [
        'young_person_id',
        'status',
        'risk_level',
        'placement_type',
        'placement_location',
        'opened_at',
        'closed_at',
        'last_reviewed_at',
        'summary',
    ];

    public function youngPerson(): BelongsTo
    {
        return $this->belongsTo(User::class, 'young_person_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'case_user',
            'case_file_id',
            'user_id'
        );
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'casefileid');
    }
}