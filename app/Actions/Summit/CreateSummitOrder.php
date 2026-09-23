<?php

namespace App\Actions\Summit;

use App\Enums\SummitOrderStatus;
use App\Enums\SummitTicketStatus;
use App\Models\PaymentSetting;
use App\Models\SummitOrder;
use App\Models\SummitTicket;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class CreateSummitOrder
{
    /** @param array<int, string> $holderNames */
    public function handle(User $buyer, array $holderNames): SummitOrder
    {
        Gate::forUser($buyer)->authorize('create', SummitOrder::class);

        $validated = Validator::make(
            ['holders' => $holderNames],
            [
                'holders' => ['required', 'array', 'min:1'],
                'holders.*' => ['required', 'string', 'max:120'],
            ],
        )->validate();

        $setting = PaymentSetting::query()
            ->where('is_active', true)
            ->latest('id')
            ->first();

        if ($setting === null || $setting->summit_ticket_price === null) {
            throw ValidationException::withMessages([
                'order' => 'Summit ticket sales are not open yet.',
            ]);
        }

        $holders = array_values(array_map(
            fn (string $name): string => trim($name),
            $validated['holders'],
        ));
        $quantity = count($holders);
        $total = bcmul((string) $setting->summit_ticket_price, (string) $quantity, 2);

        return DB::transaction(function () use ($buyer, $holders, $quantity, $setting, $total): SummitOrder {
            $order = SummitOrder::query()->create([
                'user_id' => $buyer->id,
                'quantity' => $quantity,
                'unit_price' => $setting->summit_ticket_price,
                'total_amount' => $total,
                'payment_status' => SummitOrderStatus::WaitingPayment,
            ]);

            foreach ($holders as $holder) {
                $order->tickets()->create([
                    'holder_name' => $holder,
                    'ticket_code' => $this->uniqueTicketCode(),
                    'status' => SummitTicketStatus::WaitingPayment,
                ]);
            }

            return $order->fresh('tickets');
        });
    }

    private function uniqueTicketCode(): string
    {
        for ($attempt = 0; $attempt < 10; $attempt++) {
            $code = (string) random_int(1000000000, 9999999999);

            if (! SummitTicket::query()->where('ticket_code', $code)->exists()) {
                return $code;
            }
        }

        throw new RuntimeException('A unique ticket code could not be generated.');
    }
}
