<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case WaitingVerification = 'WAITING_VERIFICATION';
    case Verified = 'VERIFIED';
    case Rejected = 'REJECTED';
}
