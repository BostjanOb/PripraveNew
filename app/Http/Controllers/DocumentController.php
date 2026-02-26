<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentFile;
use App\Support\BadgeRegistry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

class DocumentController extends Controller
{
    public function show(Document $document): \Illuminate\View\View
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

        $isOwner = auth()->id() === $document->user_id;
        $isSaved = auth()->check() && auth()->user()->savedDocuments()->where('document_id', $document->id)->exists();

        $userRating = null;
        if (auth()->check()) {
            $userRating = $document->ratings()->where('user_id', auth()->id())->value('rating');
        }

        $relatedDocuments = $document->relatedDocuments(3)->load(['category', 'schoolType', 'grade', 'subject']);

        // Author badge
        $authorBadge = null;
        if ($document->user) {
            $uploadCount = $document->user->uploadCount();
            $authorBadgeId = $this->getAuthorBadgeId($uploadCount);
            $authorBadge = $authorBadgeId ? BadgeRegistry::find($authorBadgeId) : null;
        }

        return view('pages.document-show')
            ->with('document', $document)
            ->with('isOwner', $isOwner)
            ->with('isSaved', $isSaved)
            ->with('userRating', $userRating)
            ->with('relatedDocuments', $relatedDocuments)
            ->with('authorBadge', $authorBadge);
    }

    public function downloadFile(Document $document, DocumentFile $file): StreamedResponse
    {
        abort_unless($file->document_id === $document->id, 404);
        abort_unless(auth()->check(), 403);

        $document->incrementDownloads();

        // If the storage path is a zip, extract the specific file from it
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

        $document->incrementDownloads();

        $zipName = str($document->slug)->append('.zip')->toString();
        $storagePath = $document->files->first()->storage_path;

        // If files are stored as a single zip, serve it directly
        if (str_ends_with($storagePath, '.zip') && Storage::exists($storagePath)) {
            return Storage::download($storagePath, $zipName);
        }

        // Fallback: build zip on-the-fly for legacy documents
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

    public function destroy(Request $request, Document $document): \Illuminate\Http\RedirectResponse
    {
        abort_unless(auth()->id() === $document->user_id, 403);

        $document->delete();

        return redirect()->route('profile')->with('success', 'Dokument je bil uspešno izbrisan.');
    }

    /**
     * Determine the highest contribution badge for the given upload count.
     */
    private function getAuthorBadgeId(int $uploadCount): ?string
    {
        return match (true) {
            $uploadCount >= 100 => 'mojster-priprav',
            $uploadCount >= 50 => 'zvezda-skupnosti',
            $uploadCount >= 15 => 'aktivni-avtor',
            $uploadCount >= 5 => 'prispevkar',
            $uploadCount >= 1 => 'prvi-korak',
            default => null,
        };
    }
}
