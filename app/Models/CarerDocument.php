<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarerDocument extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'original_name',
        'path',
        'size',
    ];
}
