<?php

namespace App\Jobs;

use App\Mail\AccountTransactionsMail;
use App\Models\Balance;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendAccountTransactionMailJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public User $user, public Balance $balance, public string $filePath) {}

    public function handle(): void
    {
        Mail::to($this->user->email)
            ->send(new AccountTransactionsMail($this->balance, $this->filePath));
    }
}
