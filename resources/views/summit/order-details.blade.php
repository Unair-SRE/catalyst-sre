<div class="space-y-5 text-sm">
    <dl class="grid gap-4 sm:grid-cols-2">
        <div><dt class="text-gray-500">Buyer</dt><dd class="font-medium">{{ $order->user->name }} · {{ $order->user->email }}</dd></div>
        <div><dt class="text-gray-500">Payment sender</dt><dd class="font-medium">{{ $order->sender_name ?: 'Not submitted' }}</dd></div>
        <div><dt class="text-gray-500">Quantity</dt><dd class="font-medium">{{ $order->quantity }}</dd></div>
        <div><dt class="text-gray-500">Total</dt><dd class="font-medium">IDR {{ number_format((float) $order->total_amount, 0, ',', '.') }}</dd></div>
        <div><dt class="text-gray-500">Status</dt><dd class="font-medium">{{ $order->payment_status->value }}</dd></div>
        <div><dt class="text-gray-500">Reviewed by</dt><dd class="font-medium">{{ $order->verifier?->name ?: 'Not reviewed' }}</dd></div>
    </dl>
    @if ($order->review_note)<div class="rounded border p-3"><p class="font-medium">Review note</p><p class="mt-1 whitespace-pre-line">{{ $order->review_note }}</p></div>@endif
    <div class="divide-y rounded border">
        @foreach ($order->tickets as $ticket)
            <div class="flex items-center justify-between gap-4 p-3">
                <div><p class="font-medium">{{ $ticket->holder_name }}</p><p class="text-xs text-gray-500">{{ $ticket->ticket_code }}</p></div>
                <span>{{ $ticket->status->value }}</span>
            </div>
        @endforeach
    </div>
</div>
