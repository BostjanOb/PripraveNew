<?php

namespace App\Filament\Resources\Documents\Pages;

use App\Filament\Resources\Documents\DocumentResource;
use App\Models\Document;
use Filament\Resources\Pages\EditRecord;

class EditDocument extends EditRecord
{
    protected static string $resource = DocumentResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        $document = DocumentResource::getRecordRouteBindingEloquentQuery()
            ->where((new Document)->getRouteKeyName(), $record)
            ->firstOrFail();

        $this->redirect(DocumentResource::getFrontendEditUrl($document), navigate: true);
    }
}
