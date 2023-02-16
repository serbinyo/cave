<?php

declare(strict_types=1);

namespace App\UseCase\Wim\AddExercise;

use App\DTO\Wim\LapDTO;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Команда – это простая структура данных,
 * которая представляет текущий запрос пользователя.
 */
class Command
{
    /**
     * @param UserInterface            $user
     * @param ArrayCollection|LapDTO[] $laps
     */
    public function __construct(UserInterface $user, ArrayCollection $laps)
    {
        $this->user = $user;
        $this->laps = $laps;
    }
}