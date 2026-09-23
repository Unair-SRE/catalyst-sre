<?php

namespace Database\Seeders;

use App\Models\PaymentSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentSettingSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        PaymentSetting::query()->firstOrCreate(
            ['is_active' => true],
            [
                'contact_person_name' => 'Nadia Catalyst',
                'contact_person_whatsapp' => '6281234567890',
                'summit_ticket_price' => 100000,
            ],
        );
    }
}
