<?php

declare(strict_types=1);

namespace App\DTO\Wim;

/**
 *
 */
class TestLapDTO
{
    /**
     * @param int $breaths     Количество вдохов
     * @param int $waitingTime Задержка на выдохе, сек
     */
    public function __construct(int $breaths, int $waitingTime)
    {
        $this->breaths = $breaths;
        $this->waitingTime = $waitingTime;
    }
}