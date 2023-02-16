<?php

declare(strict_types=1);

namespace App\DTO\Wim;

/**
 *
 */
class LapDTO
{
    /**
     * @param int $number      Номер круга
     * @param int $breaths     Количество вдохов
     * @param int $waitingTime Задержка на выдохе, сек
     * @param int $inhaleHold  Задержка на вдохе, сек
     */
    public function __construct(int $number, int $breaths, int $waitingTime, int $inhaleHold)
    {
        $this->number = $number;
        $this->breaths = $breaths;
        $this->waitingTime = $waitingTime;
        $this->inhaleHold = $inhaleHold;
    }
}