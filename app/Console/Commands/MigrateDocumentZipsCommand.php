<?php

namespace App\Console\Commands;

use App\Models\Document;
use App\Models\DocumentFile;
use App\Support\Documents\DocumentZipPath;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use Throwable;

class MigrateDocumentZipsCommand extends Command
{
    protected $signature = 'app:migrate-document-zips
                            {path : Path to the legacy directory containing ZIP files}';

    protected $description = 'Copy legacy document ZIP files into sharded storage paths and sync document file records';

    public function handle(): int
    {
        $sourceDirectory = $this->resolveSourceDirectory((string) $this->argument('path'));

        if ($sourceDirectory === null) {
            $this->error('The provided source directory does not exist.');

            return self::FAILURE;
        }

        $copiedCount = 0;
        $syncedCount = 0;
        $skippedCount = 0;
        $failedCount = 0;

        foreach ($this->legacyZipFiles($sourceDirectory) as $file) {
            $documentId = $this->documentIdFromFile($file);

            if ($documentId === null) {
                $this->warn("Skipping {$file->getPathname()}: filename must be a numeric document ID.");
                $skippedCount++;

                continue;
            }

            if (! Document::whereKey($documentId)->exists()) {
                $this->warn("Skipping {$file->getFilename()}: document {$documentId} was not found.");
                $skippedCount++;

                continue;
            }

            if (! DocumentFile::withTrashed()->where('document_id', $documentId)->exists()) {
                $this->warn("Skipping {$file->getFilename()}: document {$documentId} has no file records.");
                $skippedCount++;

                continue;
            }

            $destinationPath = DocumentZipPath::forDocument($documentId);
            $sourceSize = $file->getSize();

            if (Storage::exists($destinationPath)) {
                if (Storage::size($destinationPath) !== $sourceSize) {
                    $this->warn("Skipping {$file->getFilename()}: destination {$destinationPath} already exists with a different size.");
                    $skippedCount++;

                    continue;
                }
            } else {
                try {
                    $this->copyToDestination($file, $destinationPath);
                    $copiedCount++;
                } catch (Throwable $exception) {
                    $this->error("Failed to copy {$file->getFilename()}: {$exception->getMessage()}");
                    $failedCount++;

                    continue;
                }
            }

            DocumentFile::withTrashed()
                ->where('document_id', $documentId)
                ->update(['storage_path' => $destinationPath]);

            $syncedCount++;
        }

        $this->info("Copied {$copiedCount} zip(s).");
        $this->info("Synced {$syncedCount} document(s).");
        $this->info("Skipped {$skippedCount} file(s).");
        $this->info("Failed {$failedCount} file(s).");

        return $failedCount > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function resolveSourceDirectory(string $path): ?string
    {
        if (File::isDirectory($path)) {
            return realpath($path) ?: $path;
        }

        $basePath = base_path($path);

        if (File::isDirectory($basePath)) {
            return realpath($basePath) ?: $basePath;
        }

        return null;
    }

    /**
     * @return iterable<int, SplFileInfo>
     */
    private function legacyZipFiles(string $sourceDirectory): iterable
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($sourceDirectory, RecursiveDirectoryIterator::SKIP_DOTS),
        );

        foreach ($iterator as $file) {
            if (! $file instanceof SplFileInfo || ! $file->isFile()) {
                continue;
            }

            if (strtolower($file->getExtension()) !== 'zip') {
                continue;
            }

            yield $file;
        }
    }

    private function documentIdFromFile(SplFileInfo $file): ?int
    {
        $filename = $file->getBasename('.'.$file->getExtension());

        if (! preg_match('/^\d+$/', $filename)) {
            return null;
        }

        return (int) $filename;
    }

    private function copyToDestination(SplFileInfo $file, string $destinationPath): void
    {
        $stream = fopen($file->getPathname(), 'rb');

        if ($stream === false) {
            throw new \RuntimeException('Unable to open source file for reading.');
        }

        try {
            if (! Storage::put($destinationPath, $stream)) {
                throw new \RuntimeException('Unable to write ZIP file to storage.');
            }
        } finally {
            fclose($stream);
        }
    }
}
