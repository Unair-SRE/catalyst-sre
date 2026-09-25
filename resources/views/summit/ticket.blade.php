<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Summit Ticket {{ $ticket->ticket_code }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #172126; }
        .ticket { border: 2px solid #006D6A; padding: 24px; }
        .code { font-size: 28px; letter-spacing: 4px; }
        .label { color: #5b6b6b; font-size: 12px; text-transform: uppercase; }
    </style>
</head>
<body>
    <div class="ticket">
        <p class="label">Catalyst Summit</p>
        <h1>{{ $ticket->holder_name }}</h1>
        <p class="label">Ticket code</p>
        <p class="code">{{ $ticket->ticket_code }}</p>
        <p class="label">Buyer</p>
        <p>{{ $ticket->order->user->name ?? '-' }} ({{ $ticket->order->user->email ?? '-' }})</p>
    </div>
</body>
</html>
