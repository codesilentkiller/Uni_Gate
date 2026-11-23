<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationReview extends Model
{
    use HasFactory;

    protected $fillable = ['application_id','institution_id','reviewed_by','review_type','eligibility_status','recommendation','notes','reviewed_at'];
    protected function casts(): array { return ['reviewed_at'=>'datetime']; }

    public function application(): BelongsTo { return $this->belongsTo(Application::class); }
    public function institution(): BelongsTo { return $this->belongsTo(Institution::class); }
    public function reviewer(): BelongsTo { return $this->belongsTo(User::class, 'reviewed_by'); }
}
