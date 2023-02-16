<?php

declare(strict_types=1);
/**
 * Created by PhpStorm
 * User: serbin
 * Date: 31.07.2022
 * Time: 22:31
 */


namespace App\Repository\Wim;


use App\Entity\Wim\Domain\Aggregate\BreathingExercise;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Interface BreathingExerciseRepositoryInterface
 *
 * @package App\Repository\Wim
 *
 */
interface BreathingExerciseRepositoryInterface
{
    /**
     *
     */
    public function find(string $id): BreathingExercise;

    public function findOneBy(array $criteria): BreathingExercise;

    public function findAll(): ArrayCollection;

    public function findBy(array $filter): ArrayCollection;

    public function remove(string $id): bool;

    public function removeBy(array $filter): bool;

    public function getEmptyObject(): BreathingExercise;

    public function add(BreathingExercise $breathingExercise): BreathingExercise;

    public function countUserExercise(UserInterface $user): int;
}