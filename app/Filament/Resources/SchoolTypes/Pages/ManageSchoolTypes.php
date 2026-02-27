<?php

namespace App\Filament\Resources\SchoolTypes\Pages;

use App\Filament\Resources\SchoolTypes\SchoolTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageSchoolTypes extends ManageRecords
{
    protected static string $resource = SchoolTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
