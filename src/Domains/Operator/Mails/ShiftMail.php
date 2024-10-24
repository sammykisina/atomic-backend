<?php

declare(strict_types=1);

namespace Domains\Operator\Mails;

use Domains\Operator\Enums\ShiftActivities;
use Domains\SuperAdmin\Models\Shift;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

final class ShiftMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public ShiftActivities $type,
        public Shift $shift,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: match ($this->type) {
                ShiftActivities::CREATED => 'ATOMIC SHIFT CREATED',
                ShiftActivities::DELETED => 'ATOMIC SHIFT DELETED',
                ShiftActivities::EDITED => 'ATOMIC SHIFT UPDATED',
            },
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mails.operator.shift',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
