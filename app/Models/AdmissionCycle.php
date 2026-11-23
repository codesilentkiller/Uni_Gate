<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdmissionCycle extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['institution_id', 'academic_session_id', 'name', 'slug', 'opens_at', 'closes_at', 'application_fee', 'status'];

    protected function casts(): array
    {
        return [
            'opens_at' => 'datetime',
            'closes_at' => 'datetime',
            'application_fee' => 'decimal:2',
        ];
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function academicSession(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class);
    }

    public function programmes(): BelongsToMany
    {
        return $this->belongsToMany(Programme::class, 'admission_cycle_programme')
            ->withPivot('capacity')
            ->withTimestamps();
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function isOpen(): bool
    {
        return $this->status === 'open'
            && now()->between($this->opens_at, $this->closes_at);
    }
}
