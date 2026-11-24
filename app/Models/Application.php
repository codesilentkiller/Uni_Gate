<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Application extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'application_reference', 'student_id', 'applicant_id', 'institution_id', 'programme_id',
        'admission_cycle_id', 'status', 'entry_level', 'course_level', 'entry_status', 'personal_statement', 'correction_notes', 'correction_requested_at', 'applicant_confirmed_enrollment_at',
        'submitted_at', 'decision_at', 'accepted_at', 'enrolled_at', 'orientation_completed_at', 'withdrawn_at',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'decision_at' => 'datetime',
            'accepted_at' => 'datetime',
            'enrolled_at' => 'datetime',
            'withdrawn_at' => 'datetime',
            'orientation_completed_at' => 'datetime',
            'correction_requested_at' => 'datetime',
            'applicant_confirmed_enrollment_at' => 'datetime',
        ];
    }

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function programme(): BelongsTo
    {
        return $this->belongsTo(Programme::class);
    }

    public function admissionCycle(): BelongsTo
    {
        return $this->belongsTo(AdmissionCycle::class);
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(ApplicationStatusHistory::class)->latest('changed_at');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ApplicationDocument::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ApplicationReview::class)->latest('reviewed_at');
    }

    public function decision(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(AdmissionDecision::class);
    }

    public function offer(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(AdmissionOffer::class);
    }

    public function transitionTo(string $status, ?User $user = null, ?string $notes = null): bool
    {
        $allowed = [
            'draft' => ['submitted', 'withdrawn'],
            'submitted' => ['under_review', 'documents_pending', 'awaiting_payment', 'approved', 'withdrawn', 'rejected'],
            'under_review' => ['documents_pending', 'awaiting_payment', 'approved', 'rejected', 'withdrawn'],
            'documents_pending' => ['under_review', 'withdrawn'],
            'awaiting_payment' => ['under_review', 'approved', 'rejected', 'withdrawn'],
            'approved' => ['under_review', 'documents_pending', 'awaiting_payment', 'offered', 'accepted', 'rejected', 'withdrawn'],
            'offered' => ['accepted', 'declined', 'expired', 'rejected', 'withdrawn'],
            'accepted' => [],
            'declined' => [],
            'expired' => [],
            'rejected' => [],
            'withdrawn' => [],
        ];

        if (! in_array($status, $allowed[$this->status] ?? [], true)) {
            return false;
        }

        return DB::transaction(function () use ($status, $user, $notes) {
            $from = $this->status;

            $this->status = $status;
            if ($status === 'submitted') {
                $this->submitted_at = now();
            }
            if ($status === 'withdrawn') {
                $this->withdrawn_at = now();
            }
            if (in_array($status, ['approved', 'rejected'], true)) {
                $this->decision_at = now();
            }
            $this->save();

            $this->statusHistory()->create([
                'from_status' => $from,
                'to_status' => $status,
                'changed_by' => $user?->id,
                'notes' => $notes,
                'changed_at' => now(),
            ]);

            return true;
        });
    }
}
