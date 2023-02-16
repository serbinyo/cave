<?php

namespace App\UseCase\Wim\FillUserExercises;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 *
 */
class Command
{
    /**
     * @param UserInterface            $user
     */
    public function __construct(UserInterface $user)
    {
        $this->user = $user;
    }
}