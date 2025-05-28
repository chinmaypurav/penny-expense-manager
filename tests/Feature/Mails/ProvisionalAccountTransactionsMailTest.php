<?php

use App\Mail\ProvisionalAccountTransactionsMail;
use App\Models\Account;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('checks mail body', function () {
    $account = Account::factory()->create();
    $mail = new ProvisionalAccountTransactionsMail($account, 'test.csv')->render();

    $this->assertStringContainsString('Provisional Account Transactions', $mail);
});
