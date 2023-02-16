<?php

declare(strict_types=1);
/**
 * Created by PhpStorm
 * User: serbin
 * Date: 31.07.2022
 * Time: 21:39
 */


namespace App\Entity\Wim\Domain\Entity;


use App\Entity\Ulid;
use App\Entity\Wim\Domain\Aggregate\BreathingExercise;
use App\Entity\Wim\Domain\ValueObject\LapSet;
use DateInterval;
use DateTimeImmutable;
use Doctrine\ORM\Mapping\Embedded;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Lap
 *
 * @package App\Entity\Wim\Domain\Entity
 * @ORM\Entity
 * @ORM\Table(name="lap")
 */
class Lap
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", unique=true, length=36)
     *
     * @var Ulid
     */
    private Ulid $uuid;

    /**
     * @ORM\Column(type="integer")
     */
    private int $number;

    /**
     * @Embedded(class=LapSet::class, columnPrefix=false)
     */
    private LapSet $set;

    /**
     * @ORM\ManyToOne(targetEntity=BreathingExercise::class, inversedBy="laps")
     * @ORM\JoinColumn(nullable=false)
     */
    private BreathingExercise $breathingExercise;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private DateTimeImmutable $dateCreate;


    /**
     * Lap constructor.
     *
     * @param Ulid   $uuid
     * @param int    $number
     * @param LapSet $set
     */
    public function __construct(Ulid $uuid, int $number, LapSet $set)
    {
        $this->uuid = $uuid;
        $this->number = $number;
        $this->set = $set;
    }

    /**
     * @return BreathingExercise
     */
    public function getBreathingExercise(): BreathingExercise
    {
        return $this->breathingExercise;
    }

    /**
     * @param BreathingExercise $breathingExercise
     *
     * @return Lap
     */
    public function setBreathingExercise(BreathingExercise $breathingExercise): Lap
    {
        $this->breathingExercise = $breathingExercise;

        return $this;
    }

    /**
     * @return Ulid
     */
    public function getUuid(): Ulid
    {
        return $this->uuid;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getDateCreate(): DateTimeImmutable
    {
        return $this->dateCreate;
    }

    /**
     * @param DateTimeImmutable $dateCreate
     *
     * @return Lap
     */
    public function setDateCreate(DateTimeImmutable $dateCreate): Lap
    {
        $this->dateCreate = $dateCreate;

        return $this;
    }

    /**
     * @return int
     */
    public function getNumber(): int
    {
        return $this->number;
    }

    /**
     * @return int
     */
    public function getBreaths(): int
    {
        return $this->set->getBreaths();
    }

    /**
     * @return int
     */
    public function getExhaleHold(): int
    {
        return $this->set->getExhaleHold();
    }

    /**
     * @return int
     */
    public function getInhaleHold(): int
    {
        return $this->set->getInhaleHold();
    }

    /**
     * @return DateInterval
     */
    public function getLapTime(): DateInterval
    {
        return $this->set->getLapTime();
    }

    /**
     * @return LapSet
     */
    public function getSet(): LapSet
    {
        return $this->set;
    }
}
