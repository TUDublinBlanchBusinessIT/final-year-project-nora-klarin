<?php

namespace App\Models;

use App\Models\CaseFile;
use App\Models\WellbeingCheck;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WellbeingTrend extends Model
{
    use HasFactory;

    protected $table = 'wellbeing_trends';

    protected $fillable = [
        'case_file_id',
        'wellbeing_check_id',
        'overall_score',
        'overall_risk_score',
        'risk_level',
        'trend_data',
    ];

    protected $casts = [
        'trend_data' => 'array',
        'overall_score' => 'float',
        'overall_risk_score' => 'float',
    ];

    public function caseFile()
    {
        return $this->belongsTo(CaseFile::class);
    }

    public function wellbeingCheck()
    {
        return $this->belongsTo(WellbeingCheck::class);
    }
}
