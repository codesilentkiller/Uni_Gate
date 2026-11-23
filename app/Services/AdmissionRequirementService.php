<?php

namespace App\Services;

use App\Models\AdmissionRequirement;
use App\Models\Application;
use Illuminate\Support\Collection;

class AdmissionRequirementService
{
    public static function forApplication(Application $application): Collection
    {
        $cycleRequirements = AdmissionRequirement::where([
            'institution_id' => $application->institution_id,
            'admission_cycle_id' => $application->admission_cycle_id,
            'programme_id' => $application->programme_id,
        ])->get();

        if ($cycleRequirements->isNotEmpty()) {
            return $cycleRequirements;
        }

        return $application->programme->requirements()->where('institution_id', $application->institution_id)->get();
    }
}
