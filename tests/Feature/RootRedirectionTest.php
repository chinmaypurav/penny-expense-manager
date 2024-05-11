<?php

it('redirects root url to admin login', function () {
    $response = $this->get('/');

    $response->assertRedirectToRoute('filament.admin.auth.login');
});
