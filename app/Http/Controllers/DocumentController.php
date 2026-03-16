<?php

namespace App\Http\Controllers;

use App\Enums\Badge;
use App\Models\Document;
use App\Models\DocumentFile;
use App\Services\Documents\RelatedDocumentsSearchService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

class DocumentController extends Controller
{
    public function __construct(
        private RelatedDocumentsSearchService $relatedDocumentsSearchService,
    ) {}

    public function show(Document $document): View
    {
        $document->load([
            'user',
            'category',
            'schoolType',
            'grade',
            'subject',
            'files',
            'comments' => fn ($q) => $q->with('user')->latest(),
        ]);

        $document->incrementViews();

        $user = auth()->user();
        $isSaved = $user && $user->savedDocuments()->where('document_id', $document->id)->exists();
        $userRating = $user ? $document->ratings()->where('user_id', $user->id)->value('rating') : null;

        $relatedDocuments = $this->relatedDocumentsSearchService->search($document, 3);

        // Author's highest contribution badge
        $authorBadge = $document->user
            ? Badge::highestContributionBadge($document->user->uploadCount())
            : null;

        return view('pages.document-show')
            ->with('document', $document)
            ->with('isSaved', $isSaved)
            ->with('userRating', $userRating)
            ->with('relatedDocuments', $relatedDocuments)
            ->with('authorBadge', $authorBadge)
            ->with('metaDescription', $document->metaDescription())
            ->with('structuredData', $document->structuredData());
    }

    public function downloadFile(Document $document, DocumentFile $file): StreamedResponse
    {
        abort_unless($file->document_id === $document->id, 404);
        abort_unless(auth()->check(), 403);

        $document->recordDownload((int) auth()->id(), $file);

        if (str_ends_with($file->storage_path, '.zip') && Storage::exists($file->storage_path)) {
            $zipFullPath = Storage::path($file->storage_path);

            return response()->streamDownload(function () use ($zipFullPath, $file) {
                $zip = new ZipArchive;
                if ($zip->open($zipFullPath) === true) {
                    $content = $zip->getFromName($file->original_name);
                    $zip->close();
                    if ($content !== false) {
                        echo $content;
                    }
                }
            }, $file->original_name, [
                'Content-Type' => $file->mime_type,
            ]);
        }

        return Storage::download($file->storage_path, $file->original_name);
    }

    public function downloadZip(Document $document): StreamedResponse
    {
        abort_unless(auth()->check(), 403);

        $document->load('files');
        abort_if($document->files->isEmpty(), 404);

        $document->recordDownload((int) auth()->id());

        $zipName = str($document->slug)->append('.zip')->toString();
        $storagePath = $document->files->first()->storage_path;

        if (str_ends_with($storagePath, '.zip') && Storage::exists($storagePath)) {
            return Storage::download($storagePath, $zipName);
        }

        return response()->streamDownload(function () use ($document) {
            $tempPath = tempnam(sys_get_temp_dir(), 'doc_zip_');
            $zip = new ZipArchive;
            $zip->open($tempPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

            foreach ($document->files as $file) {
                if (Storage::exists($file->storage_path)) {
                    $zip->addFromString($file->original_name, Storage::get($file->storage_path));
                }
            }

            $zip->close();

            echo file_get_contents($tempPath);
            unlink($tempPath);
        }, $zipName, [
            'Content-Type' => 'application/zip',
        ]);
    }

    public function destroy(Document $document): RedirectResponse
    {
        Gate::authorize('delete', $document);

        $document->delete();

        return redirect()->route('profile')->with('success', 'Dokument je bil uspešno izbrisan.');
    }
}
