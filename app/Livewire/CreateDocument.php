<?php

namespace App\Livewire;

use App\Livewire\Forms\DocumentForm;
use App\Models\Category;
use App\Models\Document;
use App\Models\Grade;
use App\Models\SchoolType;
use App\Models\Subject;
use App\Services\Documents\DocumentFileSyncService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;

class CreateDocument extends Component
{
    use WithFileUploads;

    #[Url(as: 'uredi')]
    public ?int $editingDocumentId = null;

    public DocumentForm $form;

    public bool $submitted = false;

    public ?string $editingDocumentSlug = null;

    /** @var \Illuminate\Database\Eloquent\Collection<int, SchoolType> */
    public $schoolTypes;

    /** @var \Illuminate\Database\Eloquent\Collection<int, Grade> */
    public $grades;

    /** @var \Illuminate\Database\Eloquent\Collection<int, Category> */
    public $ostaloCategories;

    protected DocumentFileSyncService $documentFileSyncService;

    public function boot(DocumentFileSyncService $documentFileSyncService): void
    {
        $this->documentFileSyncService = $documentFileSyncService;
    }

    public function mount(): void
    {
        $this->schoolTypes = SchoolType::orderBy('sort_order')->get();
        $this->grades = Grade::orderBy('sort_order')->get();
        $this->ostaloCategories = Category::where('parent_id', 2)->orderBy('sort_order')->get();

        if ($this->editingDocumentId) {
            $document = $this->getEditableDocument();

            $this->editingDocumentSlug = $document->slug;
            $this->form->fillFromDocument($document);
        }
    }

    public function submit(): void
    {
        $this->form->validate();

        if ($this->isEditing() && $this->form->totalFilesCount() === 0) {
            $this->addError('form.files', 'Gradivo mora vsebovati vsaj eno datoteko.');

            return;
        }

        DB::transaction(function () {
            $document = $this->isEditing()
                ? $this->updateDocument()
                : $this->createDocument();

            if (! $this->isEditing() || $this->form->files !== [] || $this->form->hasRemovedExistingFiles($document)) {
                $this->documentFileSyncService->sync(
                    $document,
                    $this->form->files,
                    $this->form->retainedFileIds(),
                );
            }

            $freshDocument = $document->fresh('files');
            $this->editingDocumentSlug = $freshDocument?->slug;
            $this->form->syncExistingFiles($freshDocument ?? $document);
        });

        $this->submitted = true;
    }

    /** @return \Illuminate\Database\Eloquent\Collection<int, Subject> */
    #[Computed]
    public function filteredSubjects(): \Illuminate\Database\Eloquent\Collection
    {
        if (! $this->form->schoolTypeId) {
            return new \Illuminate\Database\Eloquent\Collection;
        }

        return Subject::orderBy('name')
            ->forSchoolType((int) $this->form->schoolTypeId)
            ->when($this->form->subjectSearch, fn ($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->get();
    }

    public function createSubject(): void
    {
        $this->validate([
            'form.subjectSearch' => ['required', 'min:2', 'max:255'],
            'form.schoolTypeId' => ['required', 'exists:school_types,id'],
        ]);

        $subject = Subject::firstOrCreate(
            [
                'name' => trim($this->form->subjectSearch),
                'school_type_id' => (int) $this->form->schoolTypeId,
            ],
        );
        $subject->schoolTypes()->syncWithoutDetaching([(int) $this->form->schoolTypeId]);

        $this->form->subjectId = (string) $subject->id;
        $this->form->subjectSearch = '';
    }

    public function removeFile(int $index): void
    {
        $this->form->removeFile($index);
    }

    public function removeExistingFile(int $fileId): void
    {
        $this->form->removeExistingFile($fileId);
    }

    public function resetForm(): void
    {
        $this->resetValidation();

        if ($this->isEditing()) {
            $document = $this->getEditableDocument();

            $this->editingDocumentSlug = $document->slug;
            $this->form->fillFromDocument($document);
            $this->submitted = false;

            return;
        }

        $this->form->reset();
        $this->submitted = false;
        $this->editingDocumentSlug = null;
    }

    public function render(): View
    {
        return view('livewire.create-document')
            ->layout('components.layouts.app', ['title' => $this->isEditing() ? 'Urejanje gradiva' : 'Dodajanje gradiva']);
    }

    public function isEditing(): bool
    {
        return $this->editingDocumentId !== null;
    }

    protected function getEditableDocument(): Document
    {
        $document = Document::with('category', 'files')->findOrFail($this->editingDocumentId);

        Gate::authorize('update', $document);

        return $document;
    }

    protected function createDocument(): Document
    {
        return Document::create([
            'user_id' => auth()->id(),
            'category_id' => $this->form->categoryId(),
            'school_type_id' => (int) $this->form->schoolTypeId,
            'grade_id' => (int) $this->form->gradeId,
            'subject_id' => (int) $this->form->subjectId,
            'title' => $this->form->title,
            'slug' => Document::generateUniqueSlug($this->form->title),
            'description' => $this->form->description ?: null,
            'topic' => $this->form->topic ?: null,
            'keywords' => $this->form->keywords ?: null,
            'downloads_count' => 0,
            'views_count' => 0,
            'rating_count' => 0,
            'rating_avg' => 0,
        ]);
    }

    protected function updateDocument(): Document
    {
        $document = $this->getEditableDocument();

        $document->update([
            'category_id' => $this->form->categoryId(),
            'school_type_id' => (int) $this->form->schoolTypeId,
            'grade_id' => (int) $this->form->gradeId,
            'subject_id' => (int) $this->form->subjectId,
            'title' => $this->form->title,
            'description' => $this->form->description ?: null,
            'topic' => $this->form->topic ?: null,
            'keywords' => $this->form->keywords ?: null,
        ]);

        $this->editingDocumentSlug = $document->slug;

        return $document;
    }
}
