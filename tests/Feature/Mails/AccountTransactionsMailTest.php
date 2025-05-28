<?php

use App\Mail\SendUserCreatedMail;
use App\Models\Balance;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('checks mail body', function () {
    $balance = Balance::factory()->create();
    $mail = new SendUserCreatedMail($balance, 'light-the-fuse')->render();

    $this->assertStringContainsString('light-the-fuse', $mail);
});
