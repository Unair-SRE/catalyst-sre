<?php

namespace App\Enums;

enum CompetitionCode: string
{
    case MiniCase = 'MCC';
    case BusinessCase = 'BCC';
    case BusinessPlan = 'BPC';

    public function isMainCompetition(): bool
    {
        return $this !== self::MiniCase;
    }

    public function label(): string
    {
        return match ($this) {
            self::MiniCase => 'Mini Case Competition',
            self::BusinessCase => 'Business Case Competition',
            self::BusinessPlan => 'Business Plan Competition',
        };
    }
}
