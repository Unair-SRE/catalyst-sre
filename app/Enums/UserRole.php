<?php

namespace App\Enums;

enum UserRole: string
{
    case Participant = 'PARTICIPANT';
    case Admin = 'ADMIN';
}
