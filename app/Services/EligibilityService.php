<?php

namespace App\Services;

use App\Models\Application;

class EligibilityService
{
    public function assess(Application $application): array
    {
        $requirements = AdmissionRequirementService::forApplication($application);

        if ($requirements->isEmpty()) {
            return [
                'status' => 'manual_review',
                'recommendation' => 'manual_review',
                'details' => 'No structured requirements are configured for this programme and admission cycle.',
            ];
        }

        $failed = [];
        $applicant = $application->applicant;

        foreach ($requirements as $requirement) {
            if (! $requirement->examination_type) {
                continue;
            }

            $results = $applicant->examinationResults()
                ->where('examination_type', $requirement->examination_type)
                ->where('verification_status', 'verified')
                ->get();

            if ($requirement->minimum_subject_count) {
                if ($results->count() < $requirement->minimum_subject_count) {
                    $failed[] = 'Insufficient verified examination subjects.';
                }
            }

            if ($requirement->subject) {
                $subjectResult = $results->firstWhere('subject', $requirement->subject);
                if (! $subjectResult) {
                    $failed[] = 'Required subject '.$requirement->subject.' was not found.';
                } elseif ($requirement->minimum_grade && $this->gradeRank($subjectResult->grade) < $this->gradeRank($requirement->minimum_grade)) {
                    $failed[] = 'Subject '.$requirement->subject.' does not meet the minimum grade.';
                }
            }
        }

        if ($failed) {
            return ['status' => 'not_eligible', 'recommendation' => 'reject', 'details' => implode(' ', $failed)];
        }

        return ['status' => 'eligible', 'recommendation' => 'approve', 'details' => 'Structured admission requirements are satisfied.'];
    }

    private function gradeRank(string $grade): int
    {
        $rank = ['A1'=>1,'B2'=>2,'B3'=>3,'C4'=>4,'C5'=>5,'C6'=>6,'D7'=>7,'E8'=>8,'F9'=>9];
        return $rank[strtoupper($grade)] ?? 10;
    }
}
