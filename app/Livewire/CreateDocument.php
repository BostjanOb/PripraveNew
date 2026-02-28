<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Document;
use App\Models\DocumentFile;
use App\Models\Grade;
use App\Models\SchoolType;
use App\Models\Subject;
use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;
use ZipArchive;

class CreateDocument extends Component
{
    use WithFileUploads;

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

    public bool $submitted = false;

    /** @var \Illuminate\Database\Eloquent\Collection<int, SchoolType> */
    public $schoolTypes;

    /** @var \Illuminate\Database\Eloquent\Collection<int, Grade> */
    public $grades;

    /** @var \Illuminate\Database\Eloquent\Collection<int, Category> */
    public $ostaloCategories;

    public function mount(): void
    {
        $this->schoolTypes = SchoolType::query()->orderBy('sort_order')->get();
        $this->grades = Grade::query()->orderBy('sort_order')->get();
        $this->ostaloCategories = Category::query()->where('parent_id', 2)->orderBy('sort_order')->get();
    }

    public function submit(): void
    {
        $allowedExtensions = implode(',', DocumentFile::ALLOWED_EXTENSIONS);

        $this->validate([
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

                    $subjectBelongsToSchoolType = Subject::query()
                        ->whereKey((int) $value)
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
            'files' => ['required', 'array', 'min:1'],
            'files.*' => ['file', 'max:20480', 'mimes:'.$allowedExtensions],
        ]);

        DB::transaction(function () {
            $categoryId = $this->categoryType === 'priprava' ? 1 : (int) $this->ostaloCategory;

            $slug = Document::generateUniqueSlug($this->title);

            $document = Document::query()->create([
                'user_id' => auth()->id(),
                'category_id' => $categoryId,
                'school_type_id' => (int) $this->schoolTypeId,
                'grade_id' => (int) $this->gradeId,
                'subject_id' => (int) $this->subjectId,
                'title' => $this->title,
                'slug' => $slug,
                'description' => $this->description ?: null,
                'topic' => $this->topic ?: null,
                'keywords' => $this->keywords ?: null,
                'downloads_count' => 0,
                'views_count' => 0,
                'rating_count' => 0,
                'rating_avg' => 0,
            ]);

            $zipDir = "documents/{$document->id}";
            $zipPath = "{$zipDir}/files.zip";

            $tempPath = tempnam(sys_get_temp_dir(), 'doc_zip_');
            $zip = new ZipArchive;
            $zip->open($tempPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

            foreach ($this->files as $file) {
                $zip->addFromString($file->getClientOriginalName(), file_get_contents($file->getRealPath()));
            }

            $zip->close();

            Storage::put($zipPath, file_get_contents($tempPath));
            unlink($tempPath);

            foreach ($this->files as $file) {
                DocumentFile::query()->create([
                    'document_id' => $document->id,
                    'original_name' => $file->getClientOriginalName(),
                    'storage_path' => $zipPath,
                    'size_bytes' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'extension' => strtolower($file->getClientOriginalExtension()),
                ]);
            }
        });

        $this->submitted = true;
    }

    /** @return \Illuminate\Database\Eloquent\Collection<int, Subject> */
    #[Computed]
    public function filteredSubjects(): \Illuminate\Database\Eloquent\Collection
    {
        if (! $this->schoolTypeId) {
            return new \Illuminate\Database\Eloquent\Collection;
        }

        return Subject::query()->forSchoolType((int) $this->schoolTypeId)
            ->when($this->subjectSearch, fn ($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->orderBy('name')
            ->get();
    }

    public function createSubject(): void
    {
        $this->validate([
            'subjectSearch' => ['required', 'min:2', 'max:255'],
            'schoolTypeId' => ['required', 'exists:school_types,id'],
        ]);

        $subject = Subject::query()->firstOrCreate(
            ['name' => trim($this->subjectSearch)],
        );
        $subject->schoolTypes()->syncWithoutDetaching([(int) $this->schoolTypeId]);

        $this->subjectId = (string) $subject->id;
        $this->subjectSearch = '';
    }

    public function removeFile(int $index): void
    {
        unset($this->files[$index]);
        $this->files = array_values($this->files);
    }

    public function resetForm(): void
    {
        $this->reset([
            'categoryType', 'ostaloCategory', 'schoolTypeId', 'gradeId',
            'subjectId', 'subjectSearch', 'title', 'topic', 'keywords',
            'description', 'files', 'submitted',
        ]);
    }

    public function render(): View
    {
        return view('livewire.create-document')
            ->layout('components.layouts.app', ['title' => 'Dodajanje gradiva']);
    }
}
