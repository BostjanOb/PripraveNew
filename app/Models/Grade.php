<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Grade extends Model
{
    /** @use HasFactory<\Database\Factories\GradeFactory> */
    use HasFactory;

    protected $fillable = [
        'school_type_id',
        'name',
        'sort_order',
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
}
