<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['name', 'slug', 'institution_code', 'email', 'phone', 'address', 'website', 'logo_path', 'primary_color', 'status'])]
class Institution extends Model
{
    use HasFactory, SoftDeletes;

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot(['status', 'joined_at'])
            ->withTimestamps();
    }

    public function staffRoles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles', 'institution_id', 'role_id')
            ->withPivot(['user_id', 'assigned_by'])
            ->withTimestamps();
    }

    public function faculties(): HasMany
    {
        return $this->hasMany(Faculty::class);
    }

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    public function programmes(): HasMany
    {
        return $this->hasMany(Programme::class);
    }

    public function admissionCycles(): HasMany
    {
        return $this->hasMany(AdmissionCycle::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function documentTypes(): HasMany
    {
        return $this->hasMany(DocumentType::class);
    }

    public function documentRequirements(): HasMany
    {
        return $this->hasMany(DocumentRequirement::class);
    }

    public function applicationDocuments(): HasMany
    {
        return $this->hasMany(ApplicationDocument::class);
    }

    public function admissionOffers(): HasMany
    {
        return $this->hasMany(AdmissionOffer::class);
    }
}
