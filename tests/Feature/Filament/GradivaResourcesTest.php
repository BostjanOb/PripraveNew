<?php

use App\Enums\ReportStatus;
use App\Filament\Resources\Comments\Pages\ManageComments;
use App\Filament\Resources\Documents\DocumentResource;
use App\Filament\Resources\Documents\Pages\CreateDocument;
use App\Filament\Resources\Documents\Pages\ListDocuments;
use App\Filament\Resources\Documents\Pages\ManageDocumentComments;
use App\Filament\Resources\Documents\Pages\ManageDocumentDownloads;
use App\Filament\Resources\Documents\Pages\ManageDocumentRatings;
use App\Filament\Resources\Documents\Pages\ViewDocument;
use App\Filament\Resources\Reports\Pages\ManageReports;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Document;
use App\Models\DownloadRecord;
use App\Models\Grade;
use App\Models\Rating;
use App\Models\Report;
use App\Models\ReportReason;
use App\Models\SchoolType;
use App\Models\Subject;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\Testing\TestAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->actingAs(User::factory()->create(['role' => 'admin']));
});

it('loads gradiva resource pages', function () {
    $comment = Comment::factory()->create();
    $document = Document::factory()->create();
    $report = Report::factory()->create();

    Livewire::test(ManageComments::class)
        ->assertOk()
        ->assertCanSeeTableRecords([$comment]);

    Livewire::test(ListDocuments::class)
        ->assertOk()
        ->assertCanSeeTableRecords([$document]);

    Livewire::test(ViewDocument::class, ['record' => $document->getRouteKey()])
        ->assertOk();

    Livewire::test(ManageDocumentDownloads::class, ['record' => $document->getRouteKey()])
        ->assertOk();

    Livewire::test(ManageDocumentComments::class, ['record' => $document->getRouteKey()])
        ->assertOk();

    Livewire::test(ManageDocumentRatings::class, ['record' => $document->getRouteKey()])
        ->assertOk();

    Livewire::test(ManageReports::class)
        ->assertOk()
        ->assertCanSeeTableRecords([$report]);
});

it('creates and updates a comment', function () {
    $document = Document::factory()->create();
    $user = User::factory()->create();

    Livewire::test(ManageComments::class)
        ->callAction(TestAction::make(CreateAction::class), data: [
            'document_id' => $document->id,
            'user_id' => $user->id,
            'text' => 'Prvi komentar',
        ])
        ->assertNotified();

    $comment = Comment::query()->where('text', 'Prvi komentar')->firstOrFail();

    Livewire::test(ManageComments::class)
        ->callAction(TestAction::make(EditAction::class)->table($comment), data: [
            'document_id' => $document->id,
            'user_id' => $user->id,
            'text' => 'Posodobljen komentar',
        ])
        ->assertNotified();

    expect($comment->fresh()->text)->toBe('Posodobljen komentar');
});

it('creates a document', function () {
    $author = User::factory()->create();
    $category = Category::factory()->create();
    $schoolType = SchoolType::factory()->create();
    $grade = Grade::factory()->create(['school_type_id' => $schoolType->id]);
    $subject = Subject::factory()->forSchoolType($schoolType)->create();

    Livewire::test(CreateDocument::class)
        ->fillForm([
            'user_id' => $author->id,
            'category_id' => $category->id,
            'school_type_id' => $schoolType->id,
            'grade_id' => $grade->id,
            'subject_id' => $subject->id,
            'title' => 'Novo gradivo',
            'slug' => 'novo-gradivo',
            'description' => 'Opis gradiva',
            'topic' => 'Tema 1',
            'keywords' => 'kemija, biologija',
        ])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertNotified();

    $document = Document::query()->where('slug', 'novo-gradivo')->firstOrFail();

    expect($document->title)->toBe('Novo gradivo');
    expect($document->grade_id)->toBe($grade->id);
});

it('shows document sub navigation pages and frontend edit link', function () {
    $document = Document::factory()->create([
        'title' => 'Relacijski test dokumenta',
    ]);
    $downloadRecord = DownloadRecord::factory()->create([
        'document_id' => $document->id,
    ]);
    $comment = Comment::factory()->create([
        'document_id' => $document->id,
        'text' => 'Komentar za preverjanje relacije',
    ]);
    $rating = Rating::factory()->create([
        'document_id' => $document->id,
        'rating' => 5,
    ]);

    Livewire::test(ViewDocument::class, ['record' => $document->getRouteKey()])
        ->assertOk()
        ->assertSee('Relacijski test dokumenta')
        ->assertSeeHtml('fi-page-has-sub-navigation')
        ->assertSeeHtml('fi-page-has-sub-navigation-start')
        ->assertSee(DocumentResource::getFrontendEditUrl($document), escape: false)
        ->assertDontSee(DocumentResource::getUrl('edit', ['record' => $document]), escape: false);

    Livewire::test(ManageDocumentDownloads::class, ['record' => $document->getRouteKey()])
        ->assertOk()
        ->assertCanSeeTableRecords([$downloadRecord]);

    Livewire::test(ManageDocumentComments::class, ['record' => $document->getRouteKey()])
        ->assertOk()
        ->assertCanSeeTableRecords([$comment]);

    Livewire::test(ManageDocumentRatings::class, ['record' => $document->getRouteKey()])
        ->assertOk()
        ->assertCanSeeTableRecords([$rating]);
});

it('creates and updates a report', function () {
    $document = Document::factory()->create();
    $user = User::factory()->create();
    $reason = ReportReason::factory()->create();

    Livewire::test(ManageReports::class)
        ->callAction(TestAction::make(CreateAction::class), data: [
            'document_id' => $document->id,
            'user_id' => $user->id,
            'report_reason_id' => $reason->id,
            'status' => ReportStatus::Pending->value,
            'message' => 'Neprimerna vsebina',
        ])
        ->assertNotified();

    $report = Report::query()->where('message', 'Neprimerna vsebina')->firstOrFail();

    Livewire::test(ManageReports::class)
        ->callAction(TestAction::make(EditAction::class)->table($report), data: [
            'document_id' => $document->id,
            'user_id' => $user->id,
            'report_reason_id' => $reason->id,
            'status' => ReportStatus::Reviewed->value,
            'message' => 'Pregledano',
        ])
        ->assertNotified();

    expect($report->fresh()->status)->toBe(ReportStatus::Reviewed);
    expect($report->fresh()->message)->toBe('Pregledano');
});
