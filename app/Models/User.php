<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasSlug, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('display_name')
            ->saveSlugsTo('slug');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    public function downloadRecords(): HasMany
    {
        return $this->hasMany(DownloadRecord::class);
    }

    public function savedDocuments(): BelongsToMany
    {
        return $this->belongsToMany(Document::class, 'saved_documents')->withTimestamps();
    }

    public function badges(): HasMany
    {
        return $this->hasMany(UserBadge::class);
    }

    public function getInitialsAttribute(): string
    {
        $words = explode(' ', trim($this->display_name));

        if (count($words) >= 2) {
            return strtoupper(mb_substr($words[0], 0, 1).mb_substr($words[1], 0, 1));
        }

        return strtoupper(mb_substr($this->display_name, 0, 2));
    }

    public function getAvatarUrlAttribute(): ?string
    {
        if ($this->avatar_path) {
            return asset('storage/'.$this->avatar_path);
        }

        return null;
    }

    public function uploadCount(): int
    {
        return $this->documents()->count();
    }

    public function downloadCount(): int
    {
        return $this->downloadRecords()->count();
    }

    public function savedCount(): int
    {
        return $this->savedDocuments()->count();
    }

    public function commentCount(): int
    {
        return $this->comments()->count();
    }

    public function memberYears(): float
    {
        return $this->created_at
            ? $this->created_at->diffInYears(now())
            : 0;
    }

    public function distinctSubjectCount(): int
    {
        return $this->documents()->distinct('subject_id')->count('subject_id');
    }

    public function maxDocumentDownloads(): int
    {
        return (int) ($this->documents()->max('downloads_count') ?? 0);
    }

    public function isPioneer(): bool
    {
        // Has a document that was the first ever in its subject+grade combination (across all users)
        return $this->documents()
            ->whereNotNull('grade_id')
            ->whereNotNull('subject_id')
            ->whereNotExists(function ($query) {
                $query->selectRaw('1')
                    ->from('documents as d2')
                    ->whereColumn('d2.subject_id', 'documents.subject_id')
                    ->whereColumn('d2.grade_id', 'documents.grade_id')
                    ->whereRaw('d2.created_at < documents.created_at');
            })
            ->exists();
    }
}
