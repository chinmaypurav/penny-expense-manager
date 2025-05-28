<?php

namespace App\Mail;

use App\Models\Account;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class AccountProvisionalTransactionsMail extends Mailable
{
    public function __construct(public Account $account, public string $filePath) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Account Provisional Transactions - {$this->account->name}}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.account-provisional-transactions',
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromStorage($this->filePath),
        ];
    }
}
