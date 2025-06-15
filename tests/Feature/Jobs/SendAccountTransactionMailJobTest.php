<?php

use App\Jobs\SendAccountTransactionMailJob;
use App\Mail\AccountTransactionsMail;
use App\Models\Account;
use App\Models\Balance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it dispatches mail', function () {
    Mail::fake();

    $user = User::factory()->createQuietly();
    $account = Account::factory()->for($user)->createQuietly();
    $balance = Balance::factory()->for($account)->createQuietly();

    SendAccountTransactionMailJob::dispatch(
        $user,
        $balance,
        $filePath = 'test.csv',
    );

    Mail::assertSent(function (AccountTransactionsMail $mail) use ($user, $balance, $filePath) {
        $this->assertTrue($mail->hasTo($user->email));
        $this->assertSame($balance->id, $mail->balance->id);
        $this->assertSame($filePath, $mail->filePath);

        return true;
    });
});
