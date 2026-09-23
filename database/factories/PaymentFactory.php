<?php

namespace Database\Factories;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\Registration;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Payment> */
class PaymentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'registration_id' => Registration::factory(),
            'sender_name' => fake()->name(),
            'payment_proof_url' => 'https://ik.imagekit.io/catalyst/competition-payments/'.fake()->uuid().'.jpg',
            'payment_proof_file_id' => fake()->uuid(),
            'status' => PaymentStatus::WaitingVerification,
        ];
    }
}
