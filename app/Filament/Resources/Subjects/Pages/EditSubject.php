<?php

namespace App\Filament\Resources\Subjects\Pages;

use App\Actions\Subjects\MergeSubjectAction;
use App\Filament\Resources\Subjects\SubjectResource;
use App\Models\Subject;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditSubject extends EditRecord
{
    protected static string $resource = SubjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('merge')
                ->label('Merge')
                ->color('warning')
                ->visible(fn (Subject $record): bool => Subject::query()->where('id', '!=', $record->getKey())->exists())
                ->schema([
                    Select::make('sourceSubjectIds')
                        ->label('Predmeti za združitev')
                        ->options(
                            fn (Subject $record): array => Subject::query()
                                ->where('id', '!=', $record->getKey())
                                ->orderBy('name')
                                ->pluck('name', 'id')
                                ->all(),
                        )
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->required()
                        ->minItems(1),
                ])
                ->requiresConfirmation()
                ->modalHeading('Združi predmete')
                ->modalDescription('Vsa povezana gradiva bodo premaknjena na trenutni predmet, izbrani predmeti pa bodo izbrisani.')
                ->modalSubmitActionLabel('Združi')
                ->action(function (array $data, Subject $record): void {
                    $mergedSubjectsCount = app(MergeSubjectAction::class)->handle(
                        targetSubject: $record,
                        sourceSubjectIds: $data['sourceSubjectIds'] ?? [],
                    );

                    $record->refresh();
                    $record->load('schoolTypes');
                    $this->refreshFormData(['name', 'schoolTypes']);

                    Notification::make()
                        ->success()
                        ->title(trans_choice('{1} Predmet je bil združen.|[2,*] Združenih je bilo :count predmetov.', $mergedSubjectsCount, [
                            'count' => $mergedSubjectsCount,
                        ]))
                        ->send();
                }),
            DeleteAction::make(),
        ];
    }
}
