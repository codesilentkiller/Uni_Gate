<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentRequirement extends Model
{
    use HasFactory;

    protected $fillable = ['institution_id','document_type_id','programme_id','admission_cycle_id','is_required','minimum_versions'];
    protected function casts(): array { return ['is_required'=>'boolean']; }

    public function institution(): BelongsTo { return $this->belongsTo(Institution::class); }
    public function documentType(): BelongsTo { return $this->belongsTo(DocumentType::class); }
    public function programme(): BelongsTo { return $this->belongsTo(Programme::class); }
    public function admissionCycle(): BelongsTo { return $this->belongsTo(AdmissionCycle::class); }
}
