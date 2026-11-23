<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdmissionOffer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['offer_reference','admission_decision_id','application_id','institution_id','entry_session_id','issued_by','offer_status','offer_message','issued_at','expires_at','accepted_at','declined_at','withdrawn_at','withdrawal_reason'];
    protected function casts(): array { return ['issued_at'=>'datetime','expires_at'=>'datetime','accepted_at'=>'datetime','declined_at'=>'datetime','withdrawn_at'=>'datetime']; }

    public function decision(): BelongsTo { return $this->belongsTo(AdmissionDecision::class, 'admission_decision_id'); }
    public function application(): BelongsTo { return $this->belongsTo(Application::class); }
    public function institution(): BelongsTo { return $this->belongsTo(Institution::class); }
    public function entrySession(): BelongsTo { return $this->belongsTo(AcademicSession::class, 'entry_session_id'); }
    public function issuer(): BelongsTo { return $this->belongsTo(User::class, 'issued_by'); }
    public function history(): HasMany { return $this->hasMany(AdmissionOfferHistory::class)->latest('changed_at'); }
}
