<?php

use App\Jobs\SendProvisionalAccountTransactionsMailJob;
use App\Mail\ProvisionalAccountTransactionsMail;
use App\Models\Account;
use App\Models\Balance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it dispatches mail', function () {
    Mail::fake();

    $user = User::factory()->createQuietly();
    $account = Account::factory()->for($user)->createQuietly();
    $balance = Balance::factory()->for($account)->periodicalRecord()->createQuietly();

    SendProvisionalAccountTransactionsMailJob::dispatch(
        $user,
        $account,
        $filePath = 'test.csv',
    );

    Mail::assertSent(function (ProvisionalAccountTransactionsMail $mail) use ($user, $account, $filePath) {
        $this->assertTrue($mail->hasTo($user->email));
        $this->assertSame($account->id, $mail->account->id);
        $this->assertSame($filePath, $mail->filePath);

        return true;
    });
});
