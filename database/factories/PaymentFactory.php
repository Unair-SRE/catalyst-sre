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
            'payment_proof_url' => null,
            'payment_proof_file_id' => null,
            'documents_submitted_at' => now(),
            'status' => PaymentStatus::WaitingVerification,
        ];
    }
}
