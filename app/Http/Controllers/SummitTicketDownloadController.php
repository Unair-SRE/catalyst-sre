<?php

namespace App\Http\Controllers;

use App\Enums\SummitTicketStatus;
use App\Models\SummitTicket;
use App\Services\SummitTicketPdf;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class SummitTicketDownloadController
{
    public function __invoke(SummitTicket $ticket, SummitTicketPdf $ticketPdf): Response
    {
        Gate::authorize('view', $ticket);
        abort_unless(
            in_array($ticket->status, [SummitTicketStatus::Active, SummitTicketStatus::Used], true),
            404,
        );

        return response($ticketPdf->render($ticket), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="summit-ticket-'.$ticket->ticket_code.'.pdf"',
            'Cache-Control' => 'private, no-store',
        ]);
    }
}
