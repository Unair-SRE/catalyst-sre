<?php

namespace App\Actions\Summit;

use App\Contracts\SummitTicketStorage;
use App\Enums\SummitOrderStatus;
use App\Enums\SummitTicketStatus;
use App\Models\SummitOrder;
use App\Models\User;
use App\Services\SummitTicketPdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;

class VerifySummitOrder
{
    public function __construct(
        private readonly SummitTicketStorage $ticketStorage,
        private readonly SummitTicketPdf $ticketPdf,
    ) {}

    public function handle(User $admin, SummitOrder $order): SummitOrder
    {
        Gate::forUser($admin)->authorize('verify', $order);

        $freshOrder = $order->fresh('tickets');

        if ($freshOrder->payment_status === SummitOrderStatus::Verified) {
            return $freshOrder;
        }

        if (! $freshOrder->hasProof() || $freshOrder->payment_status !== SummitOrderStatus::WaitingVerification) {
            throw ValidationException::withMessages([
                'order' => 'A payment proof must be submitted before verification.',
            ]);
        }

        $pdfs = [];
        foreach ($freshOrder->tickets as $ticket) {
            if (filled($ticket->pdf_file_id)) {
                continue;
            }

            $pdfs[$ticket->id] = $this->ticketStorage->store(
                $ticket->ticket_code.'.pdf',
                $this->ticketPdf->render($ticket),
            );
        }

        try {
            return DB::transaction(function () use ($admin, $order, $pdfs): SummitOrder {
                $lockedOrder = SummitOrder::query()
                    ->with('tickets')
                    ->lockForUpdate()
                    ->findOrFail($order->id);

                if ($lockedOrder->payment_status === SummitOrderStatus::Verified) {
                    return $lockedOrder;
                }

                $lockedOrder->update([
                    'payment_status' => SummitOrderStatus::Verified,
                    'verified_by' => $admin->id,
                    'verified_at' => now(),
                ]);

                foreach ($lockedOrder->tickets as $ticket) {
                    $stored = $pdfs[$ticket->id] ?? null;

                    $ticket->update(array_filter([
                        'status' => SummitTicketStatus::Active,
                        'pdf_url' => $stored?->url,
                        'pdf_file_id' => $stored?->fileId,
                    ]));
                }

                Log::info('Summit order verified.', [
                    'operation' => 'summit_order.verify',
                    'order_id' => $lockedOrder->id,
                    'admin_id' => $admin->id,
                ]);

                return $lockedOrder->fresh('tickets');
            });
        } catch (Throwable $exception) {
            foreach ($pdfs as $stored) {
                try {
                    $this->ticketStorage->delete($stored->fileId);
                } catch (Throwable $cleanupException) {
                    report($cleanupException);
                }
            }

            throw $exception;
        }
    }
}
