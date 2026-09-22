<?php

namespace App\Enums;

enum SummitTicketStatus: string
{
    case WaitingPayment = 'WAITING_PAYMENT';
    case WaitingVerification = 'WAITING_VERIFICATION';
    case Active = 'ACTIVE';
    case Used = 'USED';
    case Rejected = 'REJECTED';
}
