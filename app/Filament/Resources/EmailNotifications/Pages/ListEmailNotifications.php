<?php

namespace App\Filament\Resources\EmailNotifications\Pages;

use App\Filament\Resources\EmailNotifications\EmailNotificationResource;
use Filament\Resources\Pages\ListRecords;

class ListEmailNotifications extends ListRecords
{
    protected static string $resource = EmailNotificationResource::class;
}
