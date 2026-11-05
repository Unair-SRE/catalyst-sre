<?php

namespace App\Mail;

use App\Models\QualificationResult;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ResultPublishedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly QualificationResult $result) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Catalyst selection result: '.$this->result->round->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.result-published',
            with: ['result' => $this->result->loadMissing('registration.team', 'registration.competition', 'round')],
        );
    }
}
