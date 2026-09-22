<?php

namespace App\Enums;

enum SummitOrderStatus: string
{
    case WaitingPayment = 'WAITING_PAYMENT';
    case WaitingVerification = 'WAITING_VERIFICATION';
    case Verified = 'VERIFIED';
    case Rejected = 'REJECTED';
}
