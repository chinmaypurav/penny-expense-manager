<?php

use App\Mail\AccountTransactionsMail;
use App\Models\Balance;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('checks mail body', function () {
    $balance = Balance::factory()->create();
    $mail = new AccountTransactionsMail($balance, 'test.csv')->render();

    $this->assertStringContainsString('Periodical Account Transactions', $mail);
});
