<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CaseDocument extends Model
{
    protected $fillable = ['case_file_id', 'name', 'file_path', 'uploaded_by'];
}
