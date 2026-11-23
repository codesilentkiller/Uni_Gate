<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApplicantEducation extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'applicant_education';
    protected $fillable = ['applicant_id','institution_name','qualification_type','programme_name','start_year','end_year','result_summary','verification_status'];

    public function applicant(): BelongsTo { return $this->belongsTo(Applicant::class); }
}
