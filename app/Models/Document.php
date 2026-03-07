<?php



namespace App\Models;



use Illuminate\Database\Eloquent\Model;



class Document extends Model

{

    protected $fillable = [

        'case_file_id',

        'uploaded_by',

        'title',

        'file_path',

        'file_type',

    ];

}