<?php

namespace App\Mail;

use App\Models\SummitOrder;
use App\Services\SummitTicketPdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SummitOrderApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly SummitOrder $order) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Catalyst Summit tickets — order #'.$this->order->id,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.summit-order-approved',
            with: ['order' => $this->order->loadMissing('tickets', 'user')],
        );
    }

    /** @return array<int, Attachment> */
    public function attachments(): array
    {
        $pdf = app(SummitTicketPdf::class);

        return $this->order->tickets->map(
            fn ($ticket): Attachment => Attachment::fromData(
                fn () => $pdf->render($ticket),
                $ticket->ticket_code.'.pdf',
            )->as($ticket->ticket_code.'.pdf')->withMime('application/pdf'),
        )->all();
    }
}
