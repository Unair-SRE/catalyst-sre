<?php

namespace App\Services;

use App\Models\SummitTicket;
use Barryvdh\DomPDF\Facade\Pdf;

class SummitTicketPdf
{
    public function render(SummitTicket $ticket): string
    {
        $ticket->loadMissing('order.user');

        return Pdf::loadView('summit.ticket', ['ticket' => $ticket])->output();
    }
}
