<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    /** @use HasFactory<\Database\Factories\SubjectFactory> */
    use HasFactory;

    public function schoolTypes(): BelongsToMany
    {
        return $this->belongsToMany(SchoolType::class)->withTimestamps();
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    #[Scope]
    public function forSchoolType(Builder $query, int $schoolTypeId): Builder
    {
        return $query->whereHas(
            'schoolTypes',
            fn (Builder $schoolTypesQuery): Builder => $schoolTypesQuery->whereKey($schoolTypeId),
        );
    }
}
