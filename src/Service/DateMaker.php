<?php

declare(strict_types=1);

namespace App\Service;

use DateInterval;

/**
 *
 */
class DateMaker
{
    public static function intervalFromSeconds(int $seconds) : DateInterval
    {
        return DateInterval::createFromDateString($seconds . ' seconds');
    }

    public static function intervalEmpty() : DateInterval
    {
        return DateInterval::createFromDateString('0 seconds');
    }
}