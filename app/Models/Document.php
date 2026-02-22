<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    /** @use HasFactory<\Database\Factories\DocumentFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'topic',
        'keywords',
        'category_id',
        'school_type_id',
        'grade_id',
        'subject_id',
        'user_id',
        'views_count',
        'downloads_count',
        'rating_count',
        'rating_avg',
    ];

    protected function casts(): array
    {
        return [
            'views_count' => 'integer',
            'downloads_count' => 'integer',
            'rating_count' => 'integer',
            'rating_avg' => 'decimal:2',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function schoolType(): BelongsTo
    {
        return $this->belongsTo(SchoolType::class);
    }

    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(DocumentFile::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function savedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'saved_documents')->withTimestamps();
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function (Builder $q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhere('topic', 'like', "%{$search}%")
                ->orWhere('keywords', 'like', "%{$search}%");
        });
    }

    public function scopeForSchoolType(Builder $query, int $schoolTypeId): Builder
    {
        return $query->where('school_type_id', $schoolTypeId);
    }

    public function scopeForGrade(Builder $query, int $gradeId): Builder
    {
        return $query->where('grade_id', $gradeId);
    }

    public function scopeForSubject(Builder $query, int $subjectId): Builder
    {
        return $query->where('subject_id', $subjectId);
    }

    public function scopeForCategory(Builder $query, int $categoryId): Builder
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeSorted(Builder $query, string $sort = 'newest'): Builder
    {
        return match ($sort) {
            'oldest' => $query->oldest(),
            'most-downloaded' => $query->orderByDesc('downloads_count'),
            'most-viewed' => $query->orderByDesc('views_count'),
            default => $query->latest(),
        };
    }

    // ── Methods ───────────────────────────────────────────────────────────────

    public function incrementViews(): void
    {
        $sessionKey = "viewed_document_{$this->id}";

        if (! session()->has($sessionKey)) {
            $this->increment('views_count');
            session()->put($sessionKey, true);
        }
    }

    public function incrementDownloads(): void
    {
        $this->increment('downloads_count');
    }

    public function recalculateRating(): void
    {
        $result = $this->ratings()->selectRaw('COUNT(*) as count, AVG(rating) as avg')->first();

        $this->update([
            'rating_count' => $result->count ?? 0,
            'rating_avg' => $result->avg ? round((float) $result->avg, 2) : 0,
        ]);
    }

    public function relatedDocuments(int $limit = 4): \Illuminate\Database\Eloquent\Collection
    {
        return static::query()
            ->where('id', '!=', $this->id)
            ->where('subject_id', $this->subject_id)
            ->where('school_type_id', $this->school_type_id)
            ->latest()
            ->limit($limit)
            ->get();
    }
}
