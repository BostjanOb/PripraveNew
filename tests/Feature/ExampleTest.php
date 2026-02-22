<?php

test('the application returns a successful response', function () {
    $response = $this->get('/');

    $response->assertSuccessful();
});

test('footer profile links use named auth routes', function () {
    $response = $this->get('/');

    $response->assertSee('href="'.route('register').'"', false);
    $response->assertSee('href="'.route('login').'"', false);
});
