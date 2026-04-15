<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\CaseFile;

class Document extends Model
{
    protected $fillable = [
        'case_file_id',
        'uploaded_by',
        'title',
        'file_path',
        'file_type',
    ];

    public function caseFile()
    {
        return $this->belongsTo(CaseFile::class, 'case_file_id');
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}