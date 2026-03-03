<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'domain_id',
        'text',
        'min_value',
        'max_value',
        'is_positive',
        'risk_weight',
        'risk_level',
        'age_band_min',
        'age_band_max',
        'is_active',
        'version'
    ];

    public function domain()
    {
        return $this->belongsTo(Domain::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}