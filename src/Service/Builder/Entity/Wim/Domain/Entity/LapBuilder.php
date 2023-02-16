<?php

declare(strict_types=1);
/**
 * Created by PhpStorm
 * User: serbin
 * Date: 15.08.2022
 * Time: 18:21
 */


namespace App\Service\Builder\Entity\Wim\Domain\Entity;


use App\Entity\Ulid;
use App\Entity\Wim\Domain\Entity\Lap;
use App\Entity\Wim\Domain\ValueObject\LapSet;

/**
 *
 */
class LapBuilder
{
    /**
     *
     */
    public const UUID = '3a43f85b-28e4-48a8-b9cc-4f6bfc7e4f62';
    /**
     * @return Lap
     */
    public static function buildTestObject()
    {
        $uuid = self::UUID;
        $number = 1;
        $set = new LapSet(
            30,
            60,
            15
        );
        $lap = new Lap(
            new Ulid($uuid),
            $number,
            $set
        );

        return $lap;
    }
}
