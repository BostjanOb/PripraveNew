<?php

namespace App\Models;

use App\Support\BadgeRegistry;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserBadge extends Model
{
    /** @use HasFactory<\Database\Factories\UserBadgeFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'earned_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    /**
     * @return array{id: string, name: string, description: string, icon: string}|null
     */
    protected function definition(): Attribute
    {
        return Attribute::make(
            get: fn (): ?array => BadgeRegistry::find($this->badge_id),
        );
    }
}
