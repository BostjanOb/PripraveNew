<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    /** @use HasFactory<\Database\Factories\SubjectFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'school_type_id',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function schoolType(): BelongsTo
    {
        return $this->belongsTo(SchoolType::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeForSchoolType(Builder $query, int $schoolTypeId): Builder
    {
        return $query->where('school_type_id', $schoolTypeId);
    }
}
