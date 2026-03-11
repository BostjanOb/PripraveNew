<?php

namespace App\Actions\Subjects;

use App\Models\Document;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MergeSubjectAction
{
    /**
     * @param  array<int|string, mixed>  $sourceSubjectIds
     */
    public function handle(Subject $targetSubject, array $sourceSubjectIds): int
    {
        $mergedSubjectsCount = 0;

        $affectedDocumentIds = DB::transaction(function () use ($sourceSubjectIds, $targetSubject, &$mergedSubjectsCount): array {
            $normalizedSourceSubjectIds = collect($sourceSubjectIds)
                ->map(fn (mixed $subjectId): int => (int) $subjectId)
                ->filter(fn (int $subjectId): bool => $subjectId !== $targetSubject->getKey())
                ->unique()
                ->values();

            if ($normalizedSourceSubjectIds->isEmpty()) {
                throw ValidationException::withMessages([
                    'sourceSubjectIds' => 'Izberite vsaj en predmet za združitev.',
                ]);
            }

            $lockedTargetSubject = Subject::query()
                ->lockForUpdate()
                ->findOrFail($targetSubject->getKey());

            /** @var Collection<int, Subject> $sourceSubjects */
            $sourceSubjects = Subject::query()
                ->whereKey($normalizedSourceSubjectIds->all())
                ->with('schoolTypes:id')
                ->lockForUpdate()
                ->get();

            if ($sourceSubjects->count() !== $normalizedSourceSubjectIds->count()) {
                throw ValidationException::withMessages([
                    'sourceSubjectIds' => 'Izbrani predmeti niso več na voljo.',
                ]);
            }

            $schoolTypeIds = $sourceSubjects
                ->flatMap(fn (Subject $subject): array => $subject->schoolTypes->modelKeys())
                ->unique()
                ->values()
                ->all();

            if ($schoolTypeIds !== []) {
                $lockedTargetSubject->schoolTypes()->syncWithoutDetaching($schoolTypeIds);
            }

            $affectedDocumentIds = Document::withTrashed()
                ->whereIn('subject_id', $normalizedSourceSubjectIds->all())
                ->pluck('id')
                ->all();

            if ($affectedDocumentIds !== []) {
                Document::withTrashed()
                    ->whereKey($affectedDocumentIds)
                    ->update([
                        'subject_id' => $lockedTargetSubject->getKey(),
                    ]);
            }

            $sourceSubjects->each->delete();

            $mergedSubjectsCount = $sourceSubjects->count();

            return $affectedDocumentIds;
        });

        if ($affectedDocumentIds !== []) {
            Document::withTrashed()
                ->with('schoolType')
                ->whereKey($affectedDocumentIds)
                ->get()
                ->searchable();
        }

        return $mergedSubjectsCount;
    }
}
