<?php

use App\Filament\Resources\Categories\Pages\ManageCategories;
use App\Filament\Resources\Grades\Pages\ManageGrades;
use App\Filament\Resources\SchoolTypes\Pages\EditSchoolType;
use App\Filament\Resources\SchoolTypes\Pages\ManageSchoolTypes;
use App\Filament\Resources\Subjects\Pages\EditSubject;
use App\Filament\Resources\Subjects\Pages\ManageSubjects;
use App\Models\Category;
use App\Models\Document;
use App\Models\Grade;
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

it('loads codebook resource pages', function () {
    $schoolType = SchoolType::factory()->create();
    $grade = Grade::factory()->create();
    $subject = Subject::factory()->forSchoolType($schoolType)->create();
    $category = Category::factory()->create();

    Livewire::test(ManageSchoolTypes::class)
        ->assertOk()
        ->assertCanSeeTableRecords([$schoolType])
        ->assertSee($subject->name);

    Livewire::test(ManageGrades::class)
        ->assertOk()
        ->assertCanSeeTableRecords([$grade]);

    Livewire::test(ManageSubjects::class)
        ->assertOk()
        ->assertCanSeeTableRecords([$subject])
        ->assertSee($schoolType->name);

    Livewire::test(ManageCategories::class)
        ->assertOk()
        ->assertCanSeeTableRecords([$category]);

    Livewire::test(EditSchoolType::class, ['record' => $schoolType->getRouteKey()])
        ->assertOk()
        ->assertSee($subject->name);

    Livewire::test(EditSubject::class, ['record' => $subject->getKey()])
        ->assertOk()
        ->assertSee($schoolType->name);
});

it('creates and updates a school type', function () {
    $firstSubject = Subject::factory()->create(['name' => 'Kemija']);
    $secondSubject = Subject::factory()->create(['name' => 'Biologija']);

    Livewire::test(ManageSchoolTypes::class)
        ->callAction(TestAction::make(CreateAction::class), data: [
            'name' => 'Srednja šola',
            'slug' => 'srednja-sola',
            'sort_order' => 2,
            'subjects' => [$firstSubject->id],
        ])
        ->assertNotified();

    $schoolType = SchoolType::query()->where('slug', 'srednja-sola')->firstOrFail();

    expect($schoolType->subjects()->pluck('subjects.id')->all())->toBe([$firstSubject->id]);

    Livewire::test(EditSchoolType::class, ['record' => $schoolType->getRouteKey()])
        ->fillForm([
            'name' => 'Posodobljena srednja šola',
            'slug' => 'posodobljena-srednja-sola',
            'sort_order' => 3,
            'subjects' => [$secondSubject->id],
        ])
        ->call('save')
        ->assertNotified();

    expect($schoolType->fresh()->name)->toBe('Posodobljena srednja šola')
        ->and($schoolType->fresh()->sort_order)->toBe(3)
        ->and($schoolType->fresh()->subjects()->pluck('subjects.id')->all())->toBe([$secondSubject->id]);
});

it('creates and updates a grade', function () {
    $schoolType = SchoolType::factory()->create();

    Livewire::test(ManageGrades::class)
        ->callAction(TestAction::make(CreateAction::class), data: [
            'school_type_id' => $schoolType->id,
            'name' => '7. razred',
            'sort_order' => 7,
        ])
        ->assertNotified();

    $grade = Grade::query()
        ->where('school_type_id', $schoolType->id)
        ->where('name', '7. razred')
        ->firstOrFail();

    Livewire::test(ManageGrades::class)
        ->callAction(TestAction::make(EditAction::class)->table($grade), data: [
            'school_type_id' => $schoolType->id,
            'name' => '8. razred',
            'sort_order' => 8,
        ])
        ->assertNotified();

    expect($grade->fresh()->name)->toBe('8. razred');
    expect($grade->fresh()->sort_order)->toBe(8);
});

it('creates and updates a subject', function () {
    $firstSchoolType = SchoolType::factory()->create();
    $secondSchoolType = SchoolType::factory()->create();

    Livewire::test(ManageSubjects::class)
        ->callAction(TestAction::make(CreateAction::class), data: [
            'schoolTypes' => [$firstSchoolType->id],
            'name' => 'Kemija',
        ])
        ->assertNotified();

    $subject = Subject::query()->where('name', 'Kemija')->firstOrFail();

    expect($subject->schoolTypes()->pluck('school_types.id')->all())->toBe([$firstSchoolType->id]);

    Livewire::test(EditSubject::class, ['record' => $subject->getKey()])
        ->fillForm([
            'schoolTypes' => [$secondSchoolType->id],
            'name' => 'Biologija',
        ])
        ->call('save')
        ->assertNotified();

    expect($subject->fresh()->name)->toBe('Biologija')
        ->and($subject->fresh()->schoolTypes()->pluck('school_types.id')->all())->toBe([$secondSchoolType->id]);
});

it('merges duplicate subjects into the current subject', function () {
    $targetSchoolType = SchoolType::factory()->create();
    $sourceSchoolTypeOne = SchoolType::factory()->create();
    $sourceSchoolTypeTwo = SchoolType::factory()->create();

    $targetSubject = Subject::factory()->forSchoolType($targetSchoolType)->create([
        'name' => 'Dnevna priprava',
    ]);
    $sourceSubjectOne = Subject::factory()->forSchoolType($sourceSchoolTypeOne)->create([
        'name' => 'Dnevna priprava za ves dan',
    ]);
    $sourceSubjectTwo = Subject::factory()->forSchoolType($sourceSchoolTypeTwo)->create([
        'name' => 'Dnevne priprave',
    ]);

    $firstDocument = Document::factory()->create([
        'school_type_id' => $sourceSchoolTypeOne->id,
        'subject_id' => $sourceSubjectOne->id,
    ]);
    $secondDocument = Document::factory()->create([
        'school_type_id' => $sourceSchoolTypeTwo->id,
        'subject_id' => $sourceSubjectTwo->id,
    ]);

    Livewire::test(EditSubject::class, ['record' => $targetSubject->getKey()])
        ->callAction('merge', data: [
            'sourceSubjectIds' => [$sourceSubjectOne->id, $sourceSubjectTwo->id],
        ])
        ->assertNotified();

    expect(Subject::query()->find($targetSubject->id))->not->toBeNull()
        ->and(Subject::query()->find($sourceSubjectOne->id))->toBeNull()
        ->and(Subject::query()->find($sourceSubjectTwo->id))->toBeNull()
        ->and(
            $targetSubject->fresh()->schoolTypes()->pluck('school_types.id')->sort()->values()->all()
        )->toBe([
            $targetSchoolType->id,
            $sourceSchoolTypeOne->id,
            $sourceSchoolTypeTwo->id,
        ])
        ->and($firstDocument->fresh()->subject_id)->toBe($targetSubject->id)
        ->and($secondDocument->fresh()->subject_id)->toBe($targetSubject->id);
});

it('creates and updates a category', function () {
    $parentCategory = Category::factory()->create(['name' => 'Naravoslovje']);

    Livewire::test(ManageCategories::class)
        ->callAction(TestAction::make(CreateAction::class), data: [
            'parent_id' => $parentCategory->id,
            'name' => 'Kemija',
            'slug' => 'kemija',
            'sort_order' => 4,
        ])
        ->assertNotified();

    $category = Category::query()->where('slug', 'kemija')->firstOrFail();

    Livewire::test(ManageCategories::class)
        ->callAction(TestAction::make(EditAction::class)->table($category), data: [
            'parent_id' => null,
            'name' => 'Biologija',
            'slug' => 'biologija',
            'sort_order' => 5,
        ])
        ->assertNotified();

    expect($category->fresh()->name)->toBe('Biologija');
    expect($category->fresh()->parent_id)->toBeNull();
});
