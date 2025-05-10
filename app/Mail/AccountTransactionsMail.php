<?php

namespace App\Mail;

use App\Models\Balance;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class AccountTransactionsMail extends Mailable
{
    public function __construct(public Balance $balance, public string $filePath) {}

    public function envelope(): Envelope
    {
        $account = $this->balance->account;

        return new Envelope(
            subject: "Account Transactions - {$account->name} | {$this->balance->record_type->getLabel()} until {$this->balance->recorded_until->toDateString()}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.account-transactions',
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromStorage($this->filePath),
        ];
    }
}
