<?php

use App\Jobs\SendAccountTransactionMailJob;
use App\Mail\AccountTransactionsMail;
use App\Models\Balance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it dispatches mail', function () {
    Mail::fake();
    SendAccountTransactionMailJob::dispatch(
        $user = User::factory()->create(),
        $balance = Balance::factory()->create(),
        $filePath = 'test.csv',
    );

    Mail::assertSent(function (AccountTransactionsMail $mail) use ($user, $balance, $filePath) {
        $this->assertTrue($mail->hasTo($user->email));
        $this->assertSame($balance->id, $mail->balance->id);
        $this->assertSame($filePath, $mail->filePath);

        return true;
    });
});
