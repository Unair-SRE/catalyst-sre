<p>Hello {{ $order->user->name }},</p>
<p>Your Summit payment for order #{{ $order->id }} ({{ $order->quantity }} ticket(s)) has been verified. Each ticket PDF is attached to this email.</p>
<ul>
@foreach ($order->tickets as $ticket)
<li>{{ $ticket->holder_name }} — code {{ $ticket->ticket_code }}</li>
@endforeach
</ul>
<p>Show the PDF at check-in. See you at Catalyst Summit.</p>
