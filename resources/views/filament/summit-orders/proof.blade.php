<div class="space-y-3">
    <dl class="grid grid-cols-2 gap-2 text-sm">
        <dt class="text-gray-500">Order</dt>
        <dd>#{{ $order->id }}</dd>
        <dt class="text-gray-500">Buyer</dt>
        <dd>{{ $order->user->name }} ({{ $order->user->email }})</dd>
        <dt class="text-gray-500">Sender</dt>
        <dd>{{ $order->sender_name }}</dd>
        <dt class="text-gray-500">Total</dt>
        <dd>IDR {{ number_format((float) $order->total_amount, 2) }}</dd>
    </dl>
    @if ($proofUrl)
        <img src="{{ $proofUrl }}" alt="Payment proof" class="max-h-96 rounded border">
    @endif
</div>
