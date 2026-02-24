<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
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

    protected function casts(): array
    {
        return [
            'size_bytes' => 'integer',
        ];
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function url(): Attribute
    {
        return Attribute::make(
            get: fn () => Storage::url($this->storage_path),
        );
    }

    public function icon(): Attribute
    {
        return Attribute::make(
            get: fn () => match ($this->extension) {
                'pdf' => 'file-pdf',
                'doc', 'docx' => 'file-word',
                'ppt', 'pptx' => 'file-powerpoint',
                'xls', 'xlsx' => 'file-excel',
                'jpg', 'jpeg', 'png' => 'file-image',
                default => 'file-lines',
            },
        );
    }
}
