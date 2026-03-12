<?php

namespace App\Services\Documents;

use App\Models\Document;
use App\Models\DocumentFile;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use ZipArchive;

class DocumentFileSyncService
{
    /**
     * @param  array<int, TemporaryUploadedFile>  $uploadedFiles
     * @param  array<int, int>  $retainedFileIds
     */
    public function sync(Document $document, array $uploadedFiles, array $retainedFileIds = []): void
    {
        $document->loadMissing('files');

        $retainedFiles = $this->retainedDocumentFiles($document, $retainedFileIds);
        $zipPath = $this->storeFilesZip($document, $retainedFiles, $uploadedFiles);

        $document->files
            ->reject(fn (DocumentFile $file): bool => in_array($file->id, $retainedFileIds, true))
            ->each
            ->delete();

        $retainedFiles->each(fn (DocumentFile $file) => $file->update(['storage_path' => $zipPath]));

        foreach ($uploadedFiles as $file) {
            DocumentFile::create([
                'document_id' => $document->id,
                'original_name' => $file->getClientOriginalName(),
                'storage_path' => $zipPath,
                'size_bytes' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'extension' => strtolower($file->getClientOriginalExtension()),
            ]);
        }
    }

    /**
     * @param  array<int, int>  $retainedFileIds
     * @return Collection<int, DocumentFile>
     */
    protected function retainedDocumentFiles(Document $document, array $retainedFileIds): Collection
    {
        if ($retainedFileIds === []) {
            return new Collection;
        }

        return new Collection(
            collect($retainedFileIds)
                ->map(fn (int $fileId): ?DocumentFile => $document->files->firstWhere('id', $fileId))
                ->filter()
                ->all(),
        );
    }

    /**
     * @param  Collection<int, DocumentFile>  $retainedFiles
     * @param  array<int, TemporaryUploadedFile>  $uploadedFiles
     */
    protected function storeFilesZip(Document $document, Collection $retainedFiles, array $uploadedFiles): string
    {
        $zipPath = "documents/{$document->id}/files.zip";

        $tempPath = tempnam(sys_get_temp_dir(), 'doc_zip_');
        $zip = new ZipArchive;
        $zip->open($tempPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        foreach ($retainedFiles as $file) {
            $zip->addFromString($file->original_name, $this->readStoredFileContent($file));
        }

        foreach ($uploadedFiles as $file) {
            $zip->addFromString($file->getClientOriginalName(), file_get_contents($file->getRealPath()));
        }

        $zip->close();

        Storage::put($zipPath, file_get_contents($tempPath));
        unlink($tempPath);

        return $zipPath;
    }

    protected function readStoredFileContent(DocumentFile $file): string
    {
        if (str_ends_with($file->storage_path, '.zip') && Storage::exists($file->storage_path)) {
            $zip = new ZipArchive;
            $zip->open(Storage::path($file->storage_path));
            $content = $zip->getFromName($file->original_name);
            $zip->close();

            if ($content !== false) {
                return $content;
            }
        }

        if (Storage::exists($file->storage_path)) {
            return Storage::get($file->storage_path);
        }

        throw new \RuntimeException("Missing stored file content for document file {$file->id}.");
    }
}
