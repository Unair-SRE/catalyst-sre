<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'qris_url',
    'contact_person_name',
    'contact_person_whatsapp',
    'summit_ticket_price',
    'is_active',
])]
class PaymentSetting extends Model
{
    protected function casts(): array
    {
        return [
            'summit_ticket_price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }
}
