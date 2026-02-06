<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeeklyGoal extends Model
{
    protected $fillable = [
        'user_id',
        'goal_key',
        'week_start',
    ];
}
