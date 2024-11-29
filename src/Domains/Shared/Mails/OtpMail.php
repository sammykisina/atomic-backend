<?php

declare(strict_types=1);

namespace Domains\Shared\Mails;

use Domains\Shared\Enums\OtpTypes;
use Domains\Shared\Models\Otp;
use Domains\Shared\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

final class OtpMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public User $user,
        public Otp $otp,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->otp->type === OtpTypes::PASSWORD_RESET->value ? "ATOMIC RESET PASSWORD CODE" : 'ATOMIC VERIFICATION CODE',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mails.auth.otp',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
