<?php

use App\Filament\Resources\Documents\DocumentResource;
use App\Models\Document;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('redirects the admin document edit route to the frontend editor', function () {
    $this->actingAs(User::factory()->create(['role' => 'admin']));

    $document = Document::factory()->create();

    $this->get(DocumentResource::getUrl('edit', ['record' => $document]))
        ->assertRedirect(DocumentResource::getFrontendEditUrl($document));
});
