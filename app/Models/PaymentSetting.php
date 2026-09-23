<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'contact_person_name',
    'contact_person_whatsapp',
    'summit_ticket_price',
    'is_active',
])]
class PaymentSetting extends Model
{
    protected static function booted(): void
    {
        static::saved(function (PaymentSetting $setting): void {
            if ($setting->is_active) {
                static::query()->whereKeyNot($setting->getKey())->update(['is_active' => false]);
            }
        });
    }

    protected function casts(): array
    {
        return [
            'summit_ticket_price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }
}
