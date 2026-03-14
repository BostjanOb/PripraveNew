<?php

namespace App\Support\Documents;

class DocumentZipPath
{
    public static function forDocument(int $documentId): string
    {
        return 'documents/'.static::bucket($documentId)."/{$documentId}/files.zip";
    }

    public static function bucket(int $documentId): string
    {
        $bucket = intdiv($documentId, 10000) * 10;

        return str_pad((string) $bucket, 2, '0', STR_PAD_LEFT);
    }
}
