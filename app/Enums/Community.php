<?php

namespace App\Enums;

enum Community: string
{
    case Serving = 'Serving';

    case Reserve = 'Reserve';
    case Veteran = 'Veteran';
    case Civilian = 'Civilian';
    case Other = 'Other';

    public function variant(): string
    {
        return match ($this) {
            self::Serving => 'emerald',
            self::Reserve => 'red',
            self::Veteran => 'blue',
            self::Civilian => 'yellow',
            self::Other => 'violet',
        };
    }
}
