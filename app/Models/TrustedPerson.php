<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrustedPerson extends Model
{
    use HasFactory;

    protected $fillable = [
        'child_id',
        'name',
        'relationship',
        'phone',
        'email',
    ];

    public function child()
    {
        return $this->belongsTo(User::class, 'child_id');
    }
}
