<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Placement extends Model
{
    use HasFactory;

    protected $table = 'placements';

    protected $fillable = [
        'case_file_id',
        'type',
        'location',
        'start_date',
        'end_date',
    ];

    public function caseFile()
    {
        return $this->belongsTo(CaseFile::class, 'case_file_id');
    }
}