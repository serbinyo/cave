<?php

declare(strict_types=1);

namespace App\UseCase\Wim\FillUserExercises;

use App\Repository\Wim\BreathingExerciseRepository;
use RuntimeException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 *
 */
class Handler
{
    public BreathingExerciseRepository $breathingExerciseRepository;

    /**
     * Exerciser constructor.
     */
    public function __construct(BreathingExerciseRepository $breathingExerciseRepository)
    {
        $this->breathingExerciseRepository = $breathingExerciseRepository;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function handle(Command $command): UserInterface
    {
        /** @var UserInterface $user */
        $user = $command->user;

        if (!$user) {
            throw new RuntimeException('Не задан пользователь');
        }

        $breathingExercises = $this->breathingExerciseRepository->findBy(
            [
                'user_id' => $user->getId(),
                'limit' => 7
            ]
        );
        $user->setBreathingExercises($breathingExercises);

        return $user;
    }
}