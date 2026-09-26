<?php

namespace Database\Factories;

use App\Enums\SummitTicketStatus;
use App\Models\SummitOrder;
use App\Models\SummitTicket;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<SummitTicket> */
class SummitTicketFactory extends Factory
{
    public function definition(): array
    {
        return [
            'summit_order_id' => SummitOrder::factory(),
            'holder_name' => fake()->name(),
            'ticket_code' => (string) fake()->unique()->numberBetween(1000000000, 9999999999),
            'status' => SummitTicketStatus::Active,
        ];
    }
}
