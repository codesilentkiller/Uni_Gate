<?php

namespace App\Policies;

use App\Models\Application;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ApplicationPolicy
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
    public function view(User $user, Application $application): bool
    {
        if ($user->applicant?->is($application->applicant)) {
            return true;
        }

        return $user->institutions()
            ->whereKey($application->institution_id)
            ->wherePivot('status', 'active')
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
    public function update(User $user, Application $application): bool
    {
        if ($user->applicant?->is($application->applicant)) {
            return $application->status === 'draft';
        }

        return $user->institutions()
            ->whereKey($application->institution_id)
            ->wherePivot('status', 'active')
            ->exists();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Application $application): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Application $application): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Application $application): bool
    {
        return false;
    }

    public function withdraw(User $user, Application $application): bool
    {
        return $user->applicant?->is($application->applicant)
            && in_array($application->status, ['draft', 'submitted', 'under_review', 'documents_pending', 'awaiting_payment', 'approved'], true);
    }

    public function processInstitutionally(User $user, Application $application): bool
    {
        return $user->institutions()
            ->whereKey($application->institution_id)
            ->wherePivot('status', 'active')
            ->exists()
            && $user->roles()
                ->whereIn('roles.slug', ['admissions-officer', 'university-administrator'])
                ->where(function ($query) use ($application) {
                    $query->whereNull('user_roles.institution_id')
                        ->orWhere('user_roles.institution_id', $application->institution_id);
                })
                ->exists();
    }
}
