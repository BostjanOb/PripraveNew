<?php

namespace App\Models;

use Database\Factories\DownloadRecordFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DownloadRecord extends Model
{
    /** @use HasFactory<DownloadRecordFactory> */
    use HasFactory;

    public const UPDATED_AT = null;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function documentFile(): BelongsTo
    {
        return $this->belongsTo(DocumentFile::class);
    }
}
