<?php

namespace App\Filament\Resources\SchoolTypes\Pages;

use App\Filament\Resources\SchoolTypes\SchoolTypeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSchoolType extends EditRecord
{
    protected static string $resource = SchoolTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
