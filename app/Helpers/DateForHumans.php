<?php

namespace App\Helpers;

use Carbon\Carbon;

class DateForHumans
{
    public function humanDate(Carbon $date): string
    {
        return $date->format(
            $date->year === Carbon::now()->year
                ? 'd M, g:i A' : 'd M Y, g:i A'
        );
    }
}
