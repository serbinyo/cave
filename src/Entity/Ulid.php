<?php

/**
 *
 */

declare(strict_types=1);

namespace App\Entity;

use function is_int;

/**
 * Class Ulid
 *
 * @package Site\Entity
 */
class Ulid
{
    /**
     * @var \Symfony\Component\Uid\Ulid
     */
    protected $ulid;

    /**
     * Uuid constructor.
     *
     * @param $ulid
     */
    public function __construct($ulid = null)
    {
        if ($ulid === null) {
            $ulid = \Symfony\Component\Uid\Ulid::generate();
        } elseif ( # если число, то конвертируем в Ulid
            is_int($ulid)
            || is_numeric($ulid)
        ) {
            $ulid = sprintf("%'.026d", $ulid);
        }

        $this->ulid = \Symfony\Component\Uid\Ulid::fromString($ulid);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getUlid();
    }

    /**
     * @return string
     */
    public function getUlid(): string
    {
        return $this->ulid->toRfc4122();
    }
}
