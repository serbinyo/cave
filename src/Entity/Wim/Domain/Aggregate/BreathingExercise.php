<?php

declare(strict_types=1);

/**
 * Created by PhpStorm
 * User: serbin
 * Date: 31.07.2022
 * Time: 21:36
 */

namespace App\Entity\Wim\Domain\Aggregate;


use App\Entity\Ulid;
use App\Entity\User;
use App\Entity\Wim\Domain\Entity\Lap;
use App\Repository\Wim\BreathingExerciseRepositoryInterface;
use App\Service\DateMaker;
use DateInterval;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\Wim\BreathingExerciseRepository;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class BreathingExercise
 *
 *
 * @package App\Entity\Wim\Domain
 * @ORM\Entity(repositoryClass=BreathingExerciseRepository::class)
 * @ORM\Table(name="breathing_exercise")
 */
class BreathingExercise
{

    /**
     * @ORM\Id
     * @ORM\Column(name="id", unique=true, length=36)
     *
     * @var Ulid
     */
    public Ulid $uuid;


    /**
     * @ORM\Column(type="integer")
     */
    private ?int $sessionNumber = null;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="breathingExercises")
     * @ORM\JoinColumn(nullable=false)
     *
     */
    private User $user;

    /**
     * @ORM\OneToMany(targetEntity=Lap::class, mappedBy="breathingExercise")
     *
     * @param ArrayCollection|Lap[]
     */
    private ArrayCollection $laps;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private DateTimeImmutable $dateCreate;

    /**
     * @ORM\Column(type="string")
     */
    private ?DateInterval $duration = null;

    /**
     * BreathingExercise constructor.
     *
     * @param Ulid          $uuid
     * @param UserInterface $user
     */
    public function __construct(Ulid $uuid, UserInterface $user, DateTimeImmutable $dateCreate)
    {
        $this->uuid = $uuid;
        $this->user = $user;
        $this->dateCreate = $dateCreate;
        $this->laps = new ArrayCollection();
    }

    /**
     * Добавить новое упражнение в базу
     *
     * @param BreathingExerciseRepositoryInterface $storage
     *
     * @return $this
     */
    public function save(BreathingExerciseRepositoryInterface $storage)
    {
        if ($this->laps->isEmpty()) {
            throw new \RuntimeException('Невозможно сохранить упражнение без единого круга');
        }

        if ($this->getDuration()->s === 0) {
            throw new \RuntimeException('Невозможно сохранить упражнение нулевой продолжительности');
        }

        if ($this->getSessionNumber() === null) {
            $newSessionNumber = $storage->countUserExercise($this->getUser()) + 1;
            $this->setSessionNumber($newSessionNumber);
        }

        $storage->add($this);

        return $this;
    }

    public function addLap(Lap $lap): self
    {
        if (!$this->laps->contains($lap)) {

            if ($lap->getLapTime()->s === 0) {
                throw new \RuntimeException('Невозможно добавить пустой круг');
            }

            $this->laps[] = $lap;
            $lap->setBreathingExercise($this);
        }

        $this->duration = $this->countDuration();

        return $this;
    }

    /**
     *
     */
    public function countDuration()
    {
        $seconds = 0;

        /** @var Lap $lap */
        foreach ($this->laps as $lap) {
            $seconds += $lap->getLapTime()->s;
        }

        return DateMaker::intervalFromSeconds((int)$seconds);
    }

    /**
     * @param int $sessionNumber
     *
     * @return BreathingExercise
     */
    public function setSessionNumber(int $sessionNumber): BreathingExercise
    {
        $this->sessionNumber = $sessionNumber;

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
     * @return DateInterval
     */
    public function getDuration(): DateInterval
    {
        if (!$this->duration) {
            $this->duration = $this->countDuration();
        }
        return $this->duration;
    }

    /**
     * @return Collection<int, Lap>
     */
    public function getLaps(): Collection
    {
        return $this->laps;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return int|null
     */
    public function getSessionNumber(): ?int
    {
        return $this->sessionNumber;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getDateCreate(): DateTimeImmutable
    {
        return $this->dateCreate;
    }

    /**
     * @return int
     */
    public function getMaxExhaleHold(): int
    {
        $maxHold = 0;

        foreach ($this->getLaps() as $lap) {
            if ($lap->getExhaleHold() > $maxHold) {
                $maxHold = $lap->getExhaleHold();
            }
        }

        return $maxHold;
    }

    /**
     * @return int
     */
    public function getMaxBreaths(): int
    {
        $maxBreaths = 0;

        foreach ($this->getLaps() as $lap) {
            if ($lap->getBreaths() > $maxBreaths) {
                $maxBreaths = $lap->getBreaths();
            }
        }

        return $maxBreaths;
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return $this->getUuid()->getUlid() === '00000000-0000-0000-0000-000000000000';
    }
}