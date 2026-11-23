<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExaminationResult extends Model
{
    use HasFactory;

    protected $fillable = ['applicant_id','examination_type','examination_year','index_number','subject','grade','verification_status'];

    public function applicant(): BelongsTo { return $this->belongsTo(Applicant::class); }
}
