<?php

namespace App\Livewire\Forms;

use App\Models\Document;
use App\Models\DocumentFile;
use App\Models\Subject;
use Closure;
use Illuminate\Validation\Rule;
use Livewire\Form;

class DocumentForm extends Form
{
    public bool $editing = false;

    public string $categoryType = 'priprava';

    public string $ostaloCategory = '';

    public string $schoolTypeId = '';

    public string $gradeId = '';

    public string $subjectId = '';

    public string $subjectSearch = '';

    public string $title = '';

    public string $topic = '';

    public string $keywords = '';

    public string $description = '';

    /** @var array<int, \Livewire\Features\SupportFileUploads\TemporaryUploadedFile> */
    public array $files = [];

    /** @var array<int, array{id: int, name: string, extension: string, size: int}> */
    public array $existingFiles = [];

    protected function rules(): array
    {
        $allowedExtensions = implode(',', DocumentFile::ALLOWED_EXTENSIONS);

        return [
            'categoryType' => ['required', 'in:priprava,ostalo'],
            'ostaloCategory' => ['required_if:categoryType,ostalo', 'nullable', 'exists:categories,id'],
            'schoolTypeId' => ['required', 'exists:school_types,id'],
            'gradeId' => ['required', 'exists:grades,id'],
            'subjectId' => [
                'required',
                Rule::exists('subjects', 'id'),
                function (string $attribute, mixed $value, Closure $fail): void {
                    if (! $this->schoolTypeId) {
                        return;
                    }

                    $subjectBelongsToSchoolType = Subject::whereKey((int) $value)
                        ->forSchoolType((int) $this->schoolTypeId)
                        ->exists();

                    if (! $subjectBelongsToSchoolType) {
                        $fail('Izbran predmet ne pripada izbranemu tipu šole.');
                    }
                },
            ],
            'title' => ['required', 'max:200'],
            'topic' => ['nullable', 'max:200'],
            'keywords' => ['nullable', 'max:500'],
            'description' => ['nullable'],
            'files' => $this->isEditing() ? ['nullable', 'array'] : ['required', 'array', 'min:1'],
            'files.*' => ['file', 'max:20480', 'mimes:'.$allowedExtensions],
        ];
    }

    public function isEditing(): bool
    {
        return $this->editing;
    }

    public function categoryId(): int
    {
        return $this->categoryType === 'priprava' ? 1 : (int) $this->ostaloCategory;
    }

    public function fillFromDocument(Document $document): void
    {
        $this->editing = true;
        $this->categoryType = $document->category_id === 1 ? 'priprava' : 'ostalo';
        $this->ostaloCategory = $document->category_id === 1 ? '' : (string) $document->category_id;
        $this->schoolTypeId = (string) $document->school_type_id;
        $this->gradeId = (string) $document->grade_id;
        $this->subjectId = (string) $document->subject_id;
        $this->subjectSearch = '';
        $this->title = $document->title;
        $this->topic = $document->topic ?? '';
        $this->keywords = $document->keywords ?? '';
        $this->description = $document->description ?? '';
        $this->files = [];

        $this->syncExistingFiles($document);
    }

    public function removeFile(int $index): void
    {
        unset($this->files[$index]);
        $this->files = array_values($this->files);
    }

    public function removeExistingFile(int $fileId): void
    {
        $this->existingFiles = array_values(array_filter(
            $this->existingFiles,
            fn (array $file): bool => $file['id'] !== $fileId,
        ));
    }

    public function totalFilesCount(): int
    {
        return count($this->existingFiles) + count($this->files);
    }

    public function hasRemovedExistingFiles(Document $document): bool
    {
        return count($this->existingFiles) !== $document->files->count();
    }

    /**
     * @return array<int, int>
     */
    public function retainedFileIds(): array
    {
        return collect($this->existingFiles)
            ->pluck('id')
            ->all();
    }

    public function syncExistingFiles(Document $document): void
    {
        $document->loadMissing('files');

        $this->existingFiles = $document->files
            ->map(fn (DocumentFile $file): array => [
                'id' => $file->id,
                'name' => $file->original_name,
                'extension' => $file->extension,
                'size' => $file->size_bytes,
            ])
            ->values()
            ->all();
    }
}
