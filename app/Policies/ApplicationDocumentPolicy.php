<?php

namespace App\Policies;

use App\Models\ApplicationDocument;
use App\Models\User;

class ApplicationDocumentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ApplicationDocument $document): bool
    {
        if ($user->applicant?->id === $document->applicant_id) {
            return true;
        }

        return $user->institutions()
            ->whereKey($document->institution_id)
            ->wherePivot('status', 'active')
            ->exists()
            && $user->roles()
                ->whereIn('roles.slug', ['admissions-officer','university-administrator','document-verification-officer','read-only-staff','registrar'])
                ->where(function ($query) use ($document) {
                    $query->whereNull('user_roles.institution_id')
                        ->orWhere('user_roles.institution_id', $document->institution_id);
                })
                ->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ApplicationDocument $applicationDocument): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ApplicationDocument $applicationDocument): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ApplicationDocument $applicationDocument): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ApplicationDocument $applicationDocument): bool
    {
        return false;
    }

    public function upload(User $user, $application): bool
    {
        return $user->applicant?->id === $application->applicant_id;
    }

    public function verify(User $user, ApplicationDocument $document): bool
    {
        return $user->institutions()
            ->whereKey($document->institution_id)
            ->wherePivot('status', 'active')
            ->exists()
            && $user->roles()
                ->whereIn('roles.slug', ['university-administrator','document-verification-officer','admissions-officer'])
                ->where(function ($query) use ($document) {
                    $query->whereNull('user_roles.institution_id')
                        ->orWhere('user_roles.institution_id', $document->institution_id);
                })
                ->exists();
    }
}
