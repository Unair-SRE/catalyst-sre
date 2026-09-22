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
        PaymentSetting::query()->updateOrCreate(
            ['is_active' => true],
            [
                'qris_url' => null,
                'contact_person_name' => null,
                'contact_person_whatsapp' => null,
                'summit_ticket_price' => null,
            ],
        );
    }
}
