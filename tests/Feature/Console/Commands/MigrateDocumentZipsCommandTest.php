<?php

use App\Models\Document;
use App\Models\DocumentFile;
use App\Support\Documents\DocumentZipPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('copies nested legacy zips into sharded storage and syncs document file paths', function () {
    Storage::fake();

    $document = Document::factory()->create(['id' => 12345]);

    $firstFile = DocumentFile::factory()->create([
        'document_id' => $document->id,
        'storage_path' => 'datoteka/legacy-a',
    ]);

    $secondFile = DocumentFile::factory()->create([
        'document_id' => $document->id,
        'storage_path' => 'datoteka/legacy-b',
    ]);

    $sourceDirectory = createLegacyZipSourceDirectory();
    $zipPath = "{$sourceDirectory}/razred-10/predmet-20/{$document->id}.zip";

    try {
        File::ensureDirectoryExists(dirname($zipPath));
        File::put($zipPath, 'legacy zip content');

        $this->artisan('app:migrate-document-zips', ['path' => $sourceDirectory])
            ->expectsOutputToContain('Copied 1 zip(s).')
            ->expectsOutputToContain('Synced 1 document(s).')
            ->expectsOutputToContain('Skipped 0 file(s).')
            ->expectsOutputToContain('Failed 0 file(s).')
            ->assertSuccessful();

        $destinationPath = DocumentZipPath::forDocument($document->id);

        Storage::assertExists($destinationPath);
        expect(Storage::get($destinationPath))->toBe('legacy zip content')
            ->and(File::exists($zipPath))->toBeTrue()
            ->and($firstFile->fresh()->storage_path)->toBe($destinationPath)
            ->and($secondFile->fresh()->storage_path)->toBe($destinationPath);
    } finally {
        File::deleteDirectory($sourceDirectory);
    }
});

it('fails when the provided source directory does not exist', function () {
    $this->artisan('app:migrate-document-zips', ['path' => 'missing-document-zips'])
        ->expectsOutput('The provided source directory does not exist.')
        ->assertFailed();
});

it('skips invalid filenames, missing documents, and documents without file records', function () {
    Storage::fake();

    $documentWithoutFiles = Document::factory()->create(['id' => 22222]);
    $sourceDirectory = createLegacyZipSourceDirectory();

    try {
        File::ensureDirectoryExists("{$sourceDirectory}/razred-1/predmet-1");
        File::put("{$sourceDirectory}/razred-1/predmet-1/not-a-document.zip", 'invalid');
        File::put("{$sourceDirectory}/razred-1/predmet-1/99999.zip", 'missing document');
        File::put("{$sourceDirectory}/razred-1/predmet-1/{$documentWithoutFiles->id}.zip", 'no records');

        $this->artisan('app:migrate-document-zips', ['path' => $sourceDirectory])
            ->expectsOutputToContain('Copied 0 zip(s).')
            ->expectsOutputToContain('Synced 0 document(s).')
            ->expectsOutputToContain('Skipped 3 file(s).')
            ->expectsOutputToContain('Failed 0 file(s).')
            ->assertSuccessful();
    } finally {
        File::deleteDirectory($sourceDirectory);
    }
});

it('syncs database paths when the destination zip already exists with the same size', function () {
    Storage::fake();

    $document = Document::factory()->create(['id' => 23456]);
    $destinationPath = DocumentZipPath::forDocument($document->id);

    Storage::put($destinationPath, 'same-size');

    DocumentFile::factory()->create([
        'document_id' => $document->id,
        'storage_path' => 'datoteka/legacy-existing',
    ]);

    $sourceDirectory = createLegacyZipSourceDirectory();
    $zipPath = "{$sourceDirectory}/{$document->id}.zip";

    try {
        File::put($zipPath, 'same-size');

        $this->artisan('app:migrate-document-zips', ['path' => $sourceDirectory])
            ->expectsOutputToContain('Copied 0 zip(s).')
            ->expectsOutputToContain('Synced 1 document(s).')
            ->expectsOutputToContain('Skipped 0 file(s).')
            ->expectsOutputToContain('Failed 0 file(s).')
            ->assertSuccessful();

        expect(Storage::get($destinationPath))->toBe('same-size')
            ->and(DocumentFile::firstOrFail()->fresh()->storage_path)->toBe($destinationPath);
    } finally {
        File::deleteDirectory($sourceDirectory);
    }
});

function createLegacyZipSourceDirectory(): string
{
    $directory = sys_get_temp_dir().'/document-zips-'.str_replace('.', '', uniqid('', true));

    File::makeDirectory($directory, 0755, true);

    return $directory;
}
