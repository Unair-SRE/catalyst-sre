<?php

namespace Database\Factories;

use App\Enums\SummitOrderStatus;
use App\Models\SummitOrder;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<SummitOrder> */
class SummitOrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'quantity' => 1,
            'unit_price' => '150000.00',
            'total_amount' => '150000.00',
            'payment_status' => SummitOrderStatus::WaitingPayment,
        ];
    }
}
