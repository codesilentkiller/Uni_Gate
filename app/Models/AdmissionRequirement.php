<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdmissionRequirement extends Model
{
    use HasFactory;

    protected $fillable = ['institution_id','admission_cycle_id','programme_id','examination_type','subject','minimum_grade','minimum_subject_count','minimum_aggregate','is_required'];
    protected function casts(): array { return ['is_required'=>'boolean','minimum_aggregate'=>'decimal:2']; }

    public function institution(): BelongsTo { return $this->belongsTo(Institution::class); }
    public function admissionCycle(): BelongsTo { return $this->belongsTo(AdmissionCycle::class); }
    public function programme(): BelongsTo { return $this->belongsTo(Programme::class); }
}
