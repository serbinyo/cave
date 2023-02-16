<?php

declare(strict_types=1);
/**
 * Created by PhpStorm
 * User: serbin
 * Date: 31.07.2022
 * Time: 21:51
 */


namespace App\Entity\Wim\Domain\ValueObject;


use App\Service\DateMaker;
use DateInterval;
use Doctrine\ORM\Mapping\Embeddable;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class LapSet
 *
 * Выполненное упражнение на одном круге
 *
 * @package App\Entity\Wim\Domain\ValueObject
 * @Embeddable
 */
class LapSet
{
    /**
     * Время на один вдох
     */
    public const BREATH_TIME = 4.5;

    /**
     * @var int Количество дыханий
     *
     * @ORM\Column(name="breaths", type="integer")
     */
    private int $breaths;

    /**
     * @var int Задержка на выдохе
     *
     * @ORM\Column(name="exhale_hold", type="integer")
     */
    private int $exhaleHold;

    /**
     * @var int Задержка на вдохе
     *
     * @ORM\Column(name="inhale_hold", type="integer")
     */
    private int $inhaleHold;

    /**
     * @ORM\Column(type="string")
     */
    private ?DateInterval $time = null;


    /**
     * LapSet constructor.
     *
     * @param int $breaths
     * @param int $exhaleHold
     * @param int $inhaleHold
     */
    public function __construct(int $breaths, int $exhaleHold, int $inhaleHold)
    {
        $this->breaths = $breaths;
        $this->exhaleHold = $exhaleHold;
        $this->inhaleHold = $inhaleHold;
    }

    /**
     * @return int
     */
    public function getExhaleHold(): int
    {
        return $this->exhaleHold;
    }

    /**
     * @return int
     */
    public function getInhaleHold(): int
    {
        return $this->inhaleHold;
    }

    /**
     * @return int
     */
    public function getBreaths(): int
    {
        return $this->breaths;
    }

    /**
     * @return DateInterval
     */
    public function getLapTime(): DateInterval
    {
        if ($this->time === null) {
            $this->countTime();
        }

        return $this->time;
    }

    /**
     * Посчитать время круга
     */
    private function countTime()
    {
        $duration = $this->breaths * self::BREATH_TIME + $this->exhaleHold + $this->inhaleHold;
        $this->time = DateMaker::intervalFromSeconds((int)$duration);
    }
}
