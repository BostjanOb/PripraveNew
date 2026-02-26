<?php

it('loads terms of use page', function () {
    $this->get(route('terms'))
        ->assertSuccessful()
        ->assertSee('Pogoji uporabe')
        ->assertSee('Pravila in pogoji za uporabo spletne strani Priprave.net')
        ->assertSee('1. Splosni pogoji')
        ->assertSee('9. Koncne dolocbe');
});
