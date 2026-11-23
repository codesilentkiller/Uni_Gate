<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApplicationDocument extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['application_id','institution_id','applicant_id','document_type_id','uploaded_by','disk','stored_path','original_name','mime_type','size_bytes','checksum','version','status','verification_notes','verified_at'];
    protected function casts(): array { return ['verified_at'=>'datetime']; }

    public function application(): BelongsTo { return $this->belongsTo(Application::class); }
    public function institution(): BelongsTo { return $this->belongsTo(Institution::class); }
    public function applicant(): BelongsTo { return $this->belongsTo(Applicant::class); }
    public function documentType(): BelongsTo { return $this->belongsTo(DocumentType::class); }
    public function uploader(): BelongsTo { return $this->belongsTo(User::class, 'uploaded_by'); }
    public function verificationHistory(): HasMany { return $this->hasMany(ApplicationDocumentVerificationHistory::class)->latest('reviewed_at'); }
}
