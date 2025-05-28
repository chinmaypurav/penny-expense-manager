<?php

namespace App\Mail;

use App\Models\Account;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ProvisionalAccountTransactionsMail extends Mailable
{
    public function __construct(public Account $account, public string $filePath) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Provisional Account Transactions - {$this->account->name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.provisional-account-transactions',
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromStorage($this->filePath),
        ];
    }
}
