<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Programme extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['institution_id', 'department_id', 'name', 'code', 'award_type', 'duration_years', 'description', 'entry_requirements', 'status'];

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function admissionCycles(): BelongsToMany
    {
        return $this->belongsToMany(AdmissionCycle::class, 'admission_cycle_programme')
            ->withPivot('capacity')
            ->withTimestamps();
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function requirements(): HasMany
    {
        return $this->hasMany(ProgrammeRequirement::class);
    }
}
