<?php

namespace App\Enums;

enum EmailStatus: string
{
    case Pending = 'PENDING';
    case Sent = 'SENT';
    case Failed = 'FAILED';
}
