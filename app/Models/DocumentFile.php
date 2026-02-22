<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class DocumentFile extends Model
{
    /** @use HasFactory<\Database\Factories\DocumentFileFactory> */
    use HasFactory, SoftDeletes;

    public const ALLOWED_EXTENSIONS = ['doc', 'docx', 'pdf', 'ppt', 'pptx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png'];

    public const ALLOWED_MIMES = [
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/pdf',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'image/jpeg',
        'image/png',
    ];

    public const MAX_SIZE_BYTES = 20 * 1024 * 1024; // 20 MB

    protected $fillable = [
        'document_id',
        'original_name',
        'storage_path',
        'size_bytes',
        'mime_type',
        'extension',
    ];

    protected function casts(): array
    {
        return [
            'size_bytes' => 'integer',
        ];
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getUrlAttribute(): string
    {
        return Storage::url($this->storage_path);
    }

    public function getHumanSizeAttribute(): string
    {
        $bytes = $this->size_bytes;

        if ($bytes >= 1024 * 1024) {
            return round($bytes / (1024 * 1024), 1).' MB';
        }

        if ($bytes >= 1024) {
            return round($bytes / 1024, 1).' KB';
        }

        return $bytes.' B';
    }

    public function getIconAttribute(): string
    {
        return match ($this->extension) {
            'pdf' => 'heroicon-o-document',
            'doc', 'docx' => 'heroicon-o-document-text',
            'ppt', 'pptx' => 'heroicon-o-presentation-chart-bar',
            'xls', 'xlsx' => 'heroicon-o-table-cells',
            'jpg', 'jpeg', 'png' => 'heroicon-o-photo',
            default => 'heroicon-o-paper-clip',
        };
    }
}
