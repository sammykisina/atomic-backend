<?php

declare(strict_types=1);

namespace Domains\ReginalCivilEngineer\Mails;

use Domains\ReginalCivilEngineer\Models\InspectionSchedule;
use Domains\Shared\Enums\NotificationTypes;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

final class InspectionScheduleMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public NotificationTypes $type,
        public InspectionSchedule $inspectionSchedule,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: match ($this->type) {
                NotificationTypes::INSPECTION_SCHEDULE_CREATED => 'ATOMIC INSPECTION SCHEDULE CREATED',
            },
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mails.reginal_civil_engineer.inspection_schedule',
        );
    }

    /** @return array<int, \Illuminate\Mail\Mailables\Attachment> */
    public function attachments(): array
    {
        return [];
    }
}
