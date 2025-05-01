<?php

namespace App\Enums;

enum Membership: string
{
    case Life = 'Life';
    case Annual = 'Annual';
    case Unknown = 'Unknown';

    public function variant(): string
    {
        return match ($this) {
            self::Life => 'emerald',
            self::Annual => 'blue',
            self::Unknown => 'red',
        };
    }
}
