<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationDocumentVerificationHistory extends Model
{
    use HasFactory;

    protected $table = 'application_document_verification_history';
    protected $fillable = ['application_document_id','institution_id','reviewed_by','from_status','to_status','notes','reviewed_at'];
    protected function casts(): array { return ['reviewed_at'=>'datetime']; }

    public function document(): BelongsTo { return $this->belongsTo(ApplicationDocument::class, 'application_document_id'); }
    public function institution(): BelongsTo { return $this->belongsTo(Institution::class); }
    public function reviewer(): BelongsTo { return $this->belongsTo(User::class, 'reviewed_by'); }
}
