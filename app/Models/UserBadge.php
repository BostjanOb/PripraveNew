<?php

namespace App\Models;

use App\Enums\Badge;
use Database\Factories\UserBadgeFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserBadge extends Model
{
    /** @use HasFactory<UserBadgeFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'badge_id' => Badge::class,
            'earned_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    #[Scope]
    protected function forBadge(Builder $query, Badge $badge): Builder
    {
        return $query->where('badge_id', $badge);
    }
}
