<?php

use function Pest\Laravel\get;

it('shows remember me option on login page', function (): void {
    get(route('login'))
        ->assertOk()
        ->assertSee('name="remember"', false)
        ->assertSee('Zapomni si me');
});
