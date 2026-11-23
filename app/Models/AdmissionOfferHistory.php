<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdmissionOfferHistory extends Model
{
    use HasFactory;

    protected $table = 'admission_offer_history';
    protected $fillable = ['admission_offer_id','institution_id','changed_by','from_status','to_status','notes','changed_at'];
    protected function casts(): array { return ['changed_at'=>'datetime']; }

    public function offer(): BelongsTo { return $this->belongsTo(AdmissionOffer::class, 'admission_offer_id'); }
    public function institution(): BelongsTo { return $this->belongsTo(Institution::class); }
    public function changedBy(): BelongsTo { return $this->belongsTo(User::class, 'changed_by'); }
}
