<div>
    <x-ui.container class="py-8 sm:py-10 lg:py-14">
        <div class="mx-auto max-w-5xl space-y-8">
            <header class="max-w-3xl">
                <p class="text-xs font-medium uppercase tracking-widest text-catalyst-muted">Catalyst Summit</p>
                <h1 class="mt-4 font-display text-3xl font-medium tracking-tight sm:text-5xl">Summit Pass</h1>
                <p class="mt-4 text-base leading-7 text-catalyst-ink/75">Your access to Catalyst Summit Talkshow and Exhibition. Buy any number of tickets in one order; each ticket is assigned to its own holder.</p>
            </header>

            @if ($feedback)
                <p class="border border-status-success/30 bg-status-success/5 p-4 text-sm" role="status">{{ $feedback }}</p>
            @endif

            @if ($errors->any())
                <div class="border border-status-error/30 bg-status-error/5 p-4 text-sm text-status-error-ink" role="alert">
                    @foreach ($errors->all() as $message)<p>{{ $message }}</p>@endforeach
                </div>
            @endif

            @if ($showPurchaseForm)
                <form class="space-y-8" wire:submit="submitPurchase">
                    <section class="border border-catalyst-grey/30 bg-white p-6 sm:p-8" aria-labelledby="ticket-order-heading">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <h2 id="ticket-order-heading" class="font-display text-2xl font-semibold">Ticket holders</h2>
                                <p class="mt-2 text-sm leading-6 text-catalyst-muted">Add one full name for every person attending. There is no system ticket limit.</p>
                            </div>
                            @if ($setting?->summit_ticket_price)
                                <p class="shrink-0 text-sm"><span class="text-catalyst-muted">Price per ticket</span><br><strong>IDR {{ number_format((float) $setting->summit_ticket_price, 0, ',', '.') }}</strong></p>
                            @endif
                        </div>

                        <div class="mt-6 space-y-4">
                            @foreach ($holderNames as $index => $holderName)
                                <div class="flex items-end gap-3" wire:key="summit-holder-{{ $index }}">
                                    <label class="block flex-1 text-sm font-medium">Holder {{ $index + 1 }} full name
                                        <input class="mt-2 w-full border border-catalyst-grey/50 px-3 py-3 focus:border-catalyst-primary focus:outline-none" type="text" wire:model="holderNames.{{ $index }}" required maxlength="120">
                                    </label>
                                    @if (count($holderNames) > 1)
                                        <button class="min-h-12 border border-status-error/40 px-4 text-sm text-status-error-ink" type="button" wire:click="removeHolder({{ $index }})">Remove</button>
                                    @endif
                                </div>
                                @error('holderNames.'.$index)<p class="text-sm text-status-error-ink">{{ $message }}</p>@enderror
                            @endforeach
                        </div>

                        <button class="mt-5 border border-catalyst-primary px-4 py-3 text-sm font-medium text-catalyst-primary" type="button" wire:click="addHolder">Add another ticket</button>

                        @if ($setting?->summit_ticket_price)
                            <div class="mt-6 border-t border-catalyst-grey/30 pt-5 text-sm">
                                <span class="text-catalyst-muted">Order total for {{ count($holderNames) }} ticket(s)</span>
                                <strong class="ml-2">IDR {{ number_format((float) $setting->summit_ticket_price * count($holderNames), 0, ',', '.') }}</strong>
                            </div>
                        @endif
                    </section>

                    <section class="border border-catalyst-grey/30 bg-white p-6 sm:p-8" aria-labelledby="summit-payment-heading">
                        <h2 id="summit-payment-heading" class="font-display text-2xl font-semibold">Payment and Google Drive proof</h2>
                        <p class="mt-3 text-sm leading-6 text-catalyst-muted">Create a dedicated Google Drive folder for this order. Put the payment proof in that folder, then set access to <strong>Anyone with the link</strong> as <strong>Viewer</strong>.</p>

                        @if ($setting?->summit_ticket_price)
                            <div class="mt-6 grid gap-8 lg:grid-cols-[18rem_1fr]">
                                <div>
                                    <img class="mx-auto max-h-72 w-full object-contain" src="{{ asset(config('services.catalyst.qris_asset')) }}" alt="Catalyst payment QRIS">
                                    <p class="mt-3 text-center text-xs text-catalyst-muted">Pay the exact total shown above.</p>
                                </div>
                                <div class="space-y-5">
                                    <div class="border border-status-info/30 bg-status-info/5 p-4 text-sm leading-6">
                                        <p class="font-semibold">Folder checklist</p>
                                        <ol class="mt-2 list-decimal space-y-1 pl-5">
                                            <li>Create one folder specifically for this Summit order.</li>
                                            <li>Add the payment proof to the folder.</li>
                                            <li>Set General access to <strong>Anyone with the link</strong> and select <strong>Viewer</strong>.</li>
                                            <li>Paste the folder link below. Do not use an individual file link.</li>
                                        </ol>
                                    </div>

                                    <label class="block text-sm font-medium">Sender name
                                        <input class="mt-2 w-full border border-catalyst-grey/50 px-3 py-3 focus:border-catalyst-primary focus:outline-none" type="text" wire:model="senderName" required maxlength="120">
                                    </label>
                                    @error('senderName')<p class="text-sm text-status-error-ink">{{ $message }}</p>@enderror

                                    <label class="block text-sm font-medium">Google Drive folder link
                                        <input class="mt-2 w-full border border-catalyst-grey/50 px-3 py-3 focus:border-catalyst-primary focus:outline-none" type="url" wire:model="driveUrl" required maxlength="2048" placeholder="https://drive.google.com/drive/folders/...">
                                    </label>
                                    @error('driveUrl')<p class="text-sm text-status-error-ink">{{ $message }}</p>@enderror

                                    <label class="flex items-start gap-3 text-sm leading-6">
                                        <input class="mt-1" type="checkbox" wire:model="documentsConfirmed" required>
                                        <span>I confirm that the payment proof is inside this folder and its access is set to <strong>Anyone with the link</strong> as <strong>Viewer</strong>.</span>
                                    </label>
                                    @error('documentsConfirmed')<p class="text-sm text-status-error-ink">{{ $message }}</p>@enderror
                                </div>
                            </div>
                        @else
                            <p class="mt-6 border border-status-warning/30 bg-status-warning/5 p-4 text-sm">Summit ticket sales are not open because an active ticket price has not been configured.</p>
                        @endif
                    </section>

                    <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                        @if ($orders->isNotEmpty())
                            <button class="border border-catalyst-grey/50 px-5 py-3 text-sm" type="button" wire:click="cancelPurchase">Cancel</button>
                        @endif
                        <button class="bg-catalyst-primary px-5 py-3 text-sm font-semibold text-white disabled:opacity-50" type="submit" wire:loading.attr="disabled" @disabled(! $setting?->summit_ticket_price) wire:confirm="Submit this Summit order and payment folder for review?">Submit order for review</button>
                    </div>
                </form>
            @endif

            @if ($orders->isNotEmpty())
                <section class="space-y-5" aria-labelledby="summit-orders-heading">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 id="summit-orders-heading" class="font-display text-2xl font-semibold">Your Summit orders</h2>
                            <p class="mt-1 text-sm text-catalyst-muted">Orders and tickets are shown from the database.</p>
                        </div>
                        @unless ($showPurchaseForm)
                            <button class="bg-catalyst-primary px-4 py-3 text-sm font-medium text-white" type="button" wire:click="startPurchase">Buy more tickets</button>
                        @endunless
                    </div>

                    @foreach ($orders as $order)
                        @php
                            $status = match ($order->payment_status) {
                                \App\Enums\SummitOrderStatus::WaitingPayment => ['Waiting for payment', 'border-status-warning/30 bg-status-warning/5'],
                                \App\Enums\SummitOrderStatus::WaitingVerification => ['Payment under review', 'border-status-info/30 bg-status-info/5'],
                                \App\Enums\SummitOrderStatus::Verified => ['Verified', 'border-status-success/30 bg-status-success/5'],
                                \App\Enums\SummitOrderStatus::Rejected => ['Payment rejected', 'border-status-error/30 bg-status-error/5'],
                            };
                        @endphp
                        <article class="border border-catalyst-grey/30 bg-white p-6 sm:p-8" wire:key="summit-order-{{ $order->id }}">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                <div>
                                    <p class="text-xs uppercase tracking-wider text-catalyst-muted">Order #{{ $order->id }}</p>
                                    <h3 class="mt-2 font-display text-xl font-semibold">{{ $order->quantity }} ticket(s) · IDR {{ number_format((float) $order->total_amount, 0, ',', '.') }}</h3>
                                    <p class="mt-2 text-sm text-catalyst-muted">Created {{ $order->created_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB</p>
                                </div>
                                <span class="inline-flex self-start border px-3 py-2 text-xs font-medium {{ $status[1] }}">{{ $status[0] }}</span>
                            </div>

                            @if ($order->hasPaymentFolder())
                                <a class="mt-5 inline-flex text-sm font-medium text-catalyst-primary underline underline-offset-4" href="{{ $order->payment_drive_url }}" target="_blank" rel="noopener noreferrer">Open this order's payment folder</a>
                            @endif

                            @if ($order->payment_status === \App\Enums\SummitOrderStatus::Rejected)
                                <div class="mt-5 border border-status-error/30 bg-status-error/5 p-4 text-sm">
                                    <p class="font-semibold">The committee could not verify this payment.</p>
                                    @if ($order->review_note)<p class="mt-2 whitespace-pre-line">{{ $order->review_note }}</p>@endif
                                    <p class="mt-2">Contact the committee to provide a corrected folder. Participants cannot replace a rejected proof from this page.</p>
                                    @if ($setting?->contact_person_whatsapp)
                                        <a class="mt-3 inline-flex font-medium text-catalyst-primary underline" href="https://wa.me/{{ preg_replace('/\D+/', '', $setting->contact_person_whatsapp) }}" target="_blank" rel="noopener noreferrer">Contact {{ $setting->contact_person_name ?: 'Catalyst' }}</a>
                                    @endif
                                </div>
                            @endif

                            <div class="mt-6 grid gap-3 sm:grid-cols-2">
                                @foreach ($order->tickets as $ticket)
                                    <div class="border border-catalyst-grey/30 p-4">
                                        <p class="font-medium">{{ $ticket->holder_name }}</p>
                                        <p class="mt-1 text-xs text-catalyst-muted">{{ $ticket->status->value }}</p>
                                        @if (in_array($ticket->status, [\App\Enums\SummitTicketStatus::Active, \App\Enums\SummitTicketStatus::Used], true))
                                            <p class="mt-3 font-mono text-lg tracking-widest">{{ $ticket->ticket_code }}</p>
                                            <a class="mt-3 inline-flex text-sm font-medium text-catalyst-primary underline" href="{{ route('dashboard.summit-ticket.download', $ticket) }}">Download ticket PDF</a>
                                        @else
                                            <p class="mt-3 text-sm text-catalyst-muted">Ticket code and PDF become available after verification.</p>
                                        @endif
                                        @if ($ticket->checked_in_at)
                                            <p class="mt-3 text-xs text-catalyst-muted">Checked in {{ $ticket->checked_in_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </article>
                    @endforeach
                </section>
            @elseif (! $showPurchaseForm)
                <section class="border border-catalyst-grey/30 bg-white p-8 text-center">
                    <h2 class="font-display text-2xl font-semibold">Experience Catalyst Summit</h2>
                    <p class="mt-3 text-sm text-catalyst-muted">Purchase one or more passes for the Talkshow and Exhibition.</p>
                    <button class="mt-6 bg-catalyst-primary px-5 py-3 text-sm font-medium text-white" type="button" wire:click="startPurchase">Get Summit Pass</button>
                </section>
            @endif
        </div>
    </x-ui.container>
</div>
