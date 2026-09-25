<?php

use App\Contracts\SummitTicketStorage;
use App\Models\SummitTicket;
use App\Services\SummitTicketPdf;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Fakes\FakeSummitTicketStorage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $storage = new FakeSummitTicketStorage;
    $this->app->instance(SummitTicketStorage::class, $storage);
    $this->ticketStorage = $storage;
});

test('a ticket renders to a valid pdf document', function () {
    $ticket = SummitTicket::factory()->create();

    $html = view('summit.ticket', ['ticket' => $ticket])->render();
    $pdf = app(SummitTicketPdf::class)->render($ticket);

    expect($html)
        ->toContain($ticket->ticket_code)
        ->toContain($ticket->holder_name);

    expect($pdf)->toStartWith('%PDF')->and(strlen($pdf))->toBeGreaterThan(1000);
});

test('a rendered pdf can be stored as a private summit ticket file', function () {
    $ticket = SummitTicket::factory()->create();

    $pdf = app(SummitTicketPdf::class)->render($ticket);
    $stored = app(SummitTicketStorage::class)->store($ticket->ticket_code.'.pdf', $pdf);

    expect($stored->url)->toContain('/catalyst/summit-tickets/')
        ->and($stored->fileId)->not->toBeEmpty()
        ->and($this->ticketStorage->stored)->toHaveCount(1);
});
