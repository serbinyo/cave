<?php

declare(strict_types=1);

namespace App\Service;

/**
 *
 */
class DateConverter
{

    /**
     * Функция для склонения слова исходя из числа
     *
     * @param      $value
     * @param      $words
     * @param bool $show
     *
     * @return string
     */
    public static function numWord($value, $words, $show = true)
    {
        $num = $value % 100;
        if ($num > 19) {
            $num = $num % 10;
        }

        $out = ($show) ? $value . ' ' : '';
        $out .= match ($num) {
            1 => $words[0],
            2, 3, 4 => $words[1],
            default => $words[2],
        };

        return $out;
    }

    /**
     * @param $secs
     *
     * @return array
     */
    public static function secToArray($secs)
    {
        $res = [];

        $res['days'] = floor($secs / 86400);
        $secs = $secs % 86400;

        $res['hours'] = floor($secs / 3600);
        $secs = $secs % 3600;

        $res['minutes'] = floor($secs / 60);
        $res['secs'] = $secs % 60;

        return $res;
    }

    /**
     * Переводит секунды в строку
     *
     * @param int $secs
     *
     * @return string
     */
    public static function secToStr(int $secs)
    {
        $res = '';

        $days = floor($secs / 86400);

        if ($days > 0) {
            $secs = $secs % 86400;
            $res .= self::numWord($days, ['день', 'дня', 'дней']) . ' ';
        }


        $hours = floor($secs / 3600);

        if ($hours > 0) {
            $secs = $secs % 3600;
            $res .= self::numWord($hours, ['час', 'часа', 'часов']) . ' ';
        }

        $minutes = floor($secs / 60);

        if ($minutes > 0) {
            $secs = $secs % 60;
            $res .= self::numWord($minutes, ['минута', 'минуты', 'минут']) . ' ';
        }

        $res .= self::numWord($secs, ['секунда', 'секунды', 'секунд']);

        return $res;
    }
}