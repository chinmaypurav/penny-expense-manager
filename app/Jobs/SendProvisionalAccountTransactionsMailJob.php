<?php

namespace App\Jobs;

use App\Mail\ProvisionalAccountTransactionsMail;
use App\Models\Account;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendProvisionalAccountTransactionsMailJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public User $user, public Account $account, public string $filePath) {}

    public function handle(): void
    {
        Mail::to($this->user->email)
            ->send(new ProvisionalAccountTransactionsMail($this->account, $this->filePath));
    }
}
