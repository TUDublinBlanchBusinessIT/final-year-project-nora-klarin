<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    use HasFactory;

    protected $table = 'alerts';
    protected $fillable = ['case_file_id', 'title', 'description'];

    public function caseFile()
    {
        return $this->belongsTo(CaseFile::class, 'case_file_id');
    }

    public function youngPerson() { return $this->belongsTo(User::class, 'young_person_id'); }

    public function domain()      { return $this->belongsTo(Domain::class); }
    
    public function tag()         { return $this->belongsTo(Tag::class); }
}
