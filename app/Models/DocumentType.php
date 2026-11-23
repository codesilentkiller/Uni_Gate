<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['institution_id','name','slug','description','allowed_extensions','max_file_size_kb','status'];

    public function institution(): BelongsTo { return $this->belongsTo(Institution::class); }
    public function requirements(): HasMany { return $this->hasMany(DocumentRequirement::class); }
    public function applicationDocuments(): HasMany { return $this->hasMany(ApplicationDocument::class); }
}
