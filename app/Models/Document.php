<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Laravel\Scout\Searchable;

class Document extends Model
{
    /** @use HasFactory<\Database\Factories\DocumentFactory> */
    use HasFactory, Searchable, SoftDeletes;

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

    /**
     * @return array{id: int, title: string, description: ?string, topic: ?string, keywords: ?string, school_type_id: int, school_type_slug: ?string, grade_id: ?int, subject_id: int, category_id: int, downloads_count: int, views_count: int, created_at_ts: ?int}
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'topic' => $this->topic,
            'keywords' => $this->keywords,
            'school_type_id' => $this->school_type_id,
            'school_type_slug' => $this->schoolType?->slug,
            'grade_id' => $this->grade_id,
            'subject_id' => $this->subject_id,
            'category_id' => $this->category_id,
            'downloads_count' => $this->downloads_count,
            'views_count' => $this->views_count,
            'created_at_ts' => $this->created_at?->timestamp,
        ];
    }

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

    #[Scope]
    public function likeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function (Builder $q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhere('topic', 'like', "%{$search}%")
                ->orWhere('keywords', 'like', "%{$search}%");
        });
    }

    #[Scope]
    public function forSchoolType(Builder $query, int $schoolTypeId): Builder
    {
        return $query->where('school_type_id', $schoolTypeId);
    }

    #[Scope]
    public function forGrade(Builder $query, int $gradeId): Builder
    {
        return $query->where('grade_id', $gradeId);
    }

    #[Scope]
    public function forSubject(Builder $query, int $subjectId): Builder
    {
        return $query->where('subject_id', $subjectId);
    }

    #[Scope]
    public function forCategory(Builder $query, int $categoryId): Builder
    {
        return $query->where('category_id', $categoryId);
    }

    #[Scope]
    public function sorted(Builder $query, string $sort = 'newest'): Builder
    {
        return match ($sort) {
            'oldest' => $query->oldest(),
            'most-downloaded' => $query->orderByDesc('downloads_count'),
            'most-viewed' => $query->orderByDesc('views_count'),
            default => $query->latest(),
        };
    }

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

    public static function generateUniqueSlug(string $title): string
    {
        $slug = Str::slug($title);
        $original = $slug;
        $counter = 1;

        while (static::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $original.'-'.$counter;
            $counter++;
        }

        return $slug;
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
