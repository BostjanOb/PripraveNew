<?php

it('adds stable wire keys for document loops', function (string $viewPath, array $requiredKeys): void {
    $content = file_get_contents(__DIR__.'/../../'.$viewPath);

    expect($content)->not->toBeFalse();

    foreach ($requiredKeys as $requiredKey) {
        expect($content)->toContain($requiredKey);
    }
})->with([
    [
        'resources/views/livewire/uploaded-documents-tab.blade.php',
        ['wire:key="uploaded-document-{{ $document->id }}"'],
    ],
    [
        'resources/views/livewire/downloaded-documents-tab.blade.php',
        ['wire:key="downloaded-document-{{ $document->id }}"'],
    ],
    [
        'resources/views/livewire/saved-documents-tab.blade.php',
        ['wire:key="saved-document-{{ $document->id }}"'],
    ],
    [
        'resources/views/livewire/latest-documents.blade.php',
        [
            'wire:key="school-type-{{ $st->slug }}"',
            'wire:key="latest-document-{{ $document->id }}"',
        ],
    ],
]);
