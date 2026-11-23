<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AdmissionDecision extends Model
{
    use HasFactory;

    protected $fillable = ['application_id','institution_id','programme_id','admission_cycle_id','decided_by','approved_by','decision','decision_reason','decided_at'];
    protected function casts(): array { return ['decided_at'=>'datetime']; }

    public function application(): BelongsTo { return $this->belongsTo(Application::class); }
    public function institution(): BelongsTo { return $this->belongsTo(Institution::class); }
    public function programme(): BelongsTo { return $this->belongsTo(Programme::class); }
    public function admissionCycle(): BelongsTo { return $this->belongsTo(AdmissionCycle::class); }
    public function decidedBy(): BelongsTo { return $this->belongsTo(User::class, 'decided_by'); }
    public function approvedBy(): BelongsTo { return $this->belongsTo(User::class, 'approved_by'); }
    public function offer(): HasOne { return $this->hasOne(AdmissionOffer::class); }
}
