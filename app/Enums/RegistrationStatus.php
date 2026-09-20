<?php

namespace App\Enums;

enum RegistrationStatus: string
{
    case Pending = 'PENDING';
    case Verified = 'VERIFIED';
    case Rejected = 'REJECTED';
}
