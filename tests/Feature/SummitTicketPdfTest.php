<?php

use App\Enums\SummitTicketStatus;
use App\Models\SummitOrder;
use App\Models\SummitTicket;
use App\Models\User;
use App\Services\SummitTicketPdf;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('a ticket renders to a valid pdf document', function () {
    $ticket = SummitTicket::factory()->create();

    $html = view('summit.ticket', ['ticket' => $ticket])->render();
    $pdf = app(SummitTicketPdf::class)->render($ticket);

    expect($html)
        ->toContain($ticket->ticket_code)
        ->toContain($ticket->holder_name);
    expect($pdf)->toStartWith('%PDF')->and(strlen($pdf))->toBeGreaterThan(1000);
});

test('an active ticket pdf is generated on demand for its buyer', function () {
    $buyer = User::factory()->create();
    $order = SummitOrder::factory()->for($buyer)->create();
    $ticket = SummitTicket::factory()
        ->for($order, 'order')
        ->create(['status' => SummitTicketStatus::Active, 'pdf_url' => null, 'pdf_file_id' => null]);

    $response = $this->actingAs($buyer)->get(route('dashboard.summit-ticket.download', $ticket));

    $response->assertOk()
        ->assertHeader('Content-Type', 'application/pdf')
        ->assertHeader('Content-Disposition', 'attachment; filename="summit-ticket-'.$ticket->ticket_code.'.pdf"');

    expect($response->getContent())->toStartWith('%PDF');
});

test('a user cannot download another buyer ticket', function () {
    $order = SummitOrder::factory()->for(User::factory())->create();
    $ticket = SummitTicket::factory()->for($order, 'order')->create([
        'status' => SummitTicketStatus::Active,
    ]);

    $this->actingAs(User::factory()->create())
        ->get(route('dashboard.summit-ticket.download', $ticket))
        ->assertForbidden();
});
