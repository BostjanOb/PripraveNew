<?php

use App\Filament\Resources\Categories\Pages\ManageCategories;
use App\Filament\Resources\Grades\Pages\ManageGrades;
use App\Filament\Resources\SchoolTypes\Pages\ManageSchoolTypes;
use App\Filament\Resources\Subjects\Pages\ManageSubjects;
use App\Models\Category;
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
    $subject = Subject::factory()->create();
    $category = Category::factory()->create();

    Livewire::test(ManageSchoolTypes::class)
        ->assertOk()
        ->assertCanSeeTableRecords([$schoolType]);

    Livewire::test(ManageGrades::class)
        ->assertOk()
        ->assertCanSeeTableRecords([$grade]);

    Livewire::test(ManageSubjects::class)
        ->assertOk()
        ->assertCanSeeTableRecords([$subject]);

    Livewire::test(ManageCategories::class)
        ->assertOk()
        ->assertCanSeeTableRecords([$category]);
});

it('creates and updates a school type', function () {
    Livewire::test(ManageSchoolTypes::class)
        ->callAction(TestAction::make(CreateAction::class), data: [
            'name' => 'Srednja šola',
            'slug' => 'srednja-sola',
            'sort_order' => 2,
        ])
        ->assertNotified();

    $schoolType = SchoolType::query()->where('slug', 'srednja-sola')->firstOrFail();

    Livewire::test(ManageSchoolTypes::class)
        ->callAction(TestAction::make(EditAction::class)->table($schoolType), data: [
            'name' => 'Posodobljena srednja šola',
            'slug' => 'posodobljena-srednja-sola',
            'sort_order' => 3,
        ])
        ->assertNotified();

    expect($schoolType->fresh()->name)->toBe('Posodobljena srednja šola');
    expect($schoolType->fresh()->sort_order)->toBe(3);
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
    $schoolType = SchoolType::factory()->create();

    Livewire::test(ManageSubjects::class)
        ->callAction(TestAction::make(CreateAction::class), data: [
            'school_type_id' => $schoolType->id,
            'name' => 'Kemija',
        ])
        ->assertNotified();

    $subject = Subject::query()
        ->where('school_type_id', $schoolType->id)
        ->where('name', 'Kemija')
        ->firstOrFail();

    Livewire::test(ManageSubjects::class)
        ->callAction(TestAction::make(EditAction::class)->table($subject), data: [
            'school_type_id' => $schoolType->id,
            'name' => 'Biologija',
        ])
        ->assertNotified();

    expect($subject->fresh()->name)->toBe('Biologija');
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
