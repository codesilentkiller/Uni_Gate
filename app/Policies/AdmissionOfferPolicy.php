<?php

namespace App\Policies;

use App\Models\AdmissionOffer;
use App\Models\User;

class AdmissionOfferPolicy
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
    public function view(User $user, AdmissionOffer $offer): bool
    {
        if ($user->applicant?->id === $offer->application->applicant_id) return true;

        return $user->institutions()->whereKey($offer->institution_id)->wherePivot('status','active')->exists()
            && $user->roles()->whereIn('roles.slug',['university-administrator','admissions-officer','registrar','read-only-staff'])
                ->where(function ($query) use ($offer) {
                    $query->whereNull('user_roles.institution_id')->orWhere('user_roles.institution_id',$offer->institution_id);
                })->exists();
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
    public function update(User $user, AdmissionOffer $admissionOffer): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AdmissionOffer $admissionOffer): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, AdmissionOffer $admissionOffer): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, AdmissionOffer $admissionOffer): bool
    {
        return false;
    }

    public function issue(User $user, AdmissionOffer $offer): bool
    {
        return $user->institutions()->whereKey($offer->institution_id)->wherePivot('status','active')->exists()
            && $user->roles()->whereIn('roles.slug',['university-administrator','admissions-officer'])
                ->where(function ($query) use ($offer) {
                    $query->whereNull('user_roles.institution_id')->orWhere('user_roles.institution_id',$offer->institution_id);
                })->exists();
    }

    public function respond(User $user, AdmissionOffer $offer): bool
    {
        return $user->applicant?->id === $offer->application->applicant_id && $offer->offer_status === 'issued';
    }

    public function withdraw(User $user, AdmissionOffer $offer): bool
    {
        return $this->issue($user,$offer) && in_array($offer->offer_status,['draft','issued'],true);
    }
}
