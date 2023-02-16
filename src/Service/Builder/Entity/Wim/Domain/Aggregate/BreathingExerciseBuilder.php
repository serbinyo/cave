<?php

declare(strict_types=1);

namespace App\Service\Builder\Entity\Wim\Domain\Aggregate;

use App\Entity\Ulid;
use App\Entity\User;
use App\Entity\Wim\Domain\Aggregate\BreathingExercise;
use App\Service\Builder\Entity\Wim\Domain\Entity\LapBuilder;
use DateTimeImmutable;

/**
 *
 */
class BreathingExerciseBuilder
{
    /**
     *
     */
    public const UUID = '57d8a1e6-9366-4c7f-98a8-092f87878e6d';
    /**
     * @return BreathingExercise
     */
    public static function buildTestObject()
    {
        $exercise = new BreathingExercise(
            new Ulid(self::UUID),
            (new User())
                ->setId(1)
                ->setEmail('email@sevastopol.mir')
                ->setName('Вим Хоф'),
            new DateTimeImmutable('9999-12-31')
        );

        $exercise->addLap(LapBuilder::buildTestObject());
        $exercise->setSessionNumber(123);

        return $exercise;
    }
}