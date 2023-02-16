<?php

declare(strict_types=1);

/**
 * Created by PhpStorm
 * User: serbin
 * Date: 31.07.2022
 * Time: 22:29
 */

namespace App\Repository\Wim;


use App\Entity\Ulid;
use App\Entity\User;
use App\Entity\Wim\Domain\Aggregate\BreathingExercise;
use App\Entity\Wim\Domain\Entity\Lap;
use App\Entity\Wim\Domain\ValueObject\LapSet;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class BreathingExerciseRepository
 *
 * @package App\Repository\Wim
 *
 */
class BreathingExerciseRepository implements BreathingExerciseRepositoryInterface
{
    /**
     *
     */
    public const TABLE = 'breathing_exercise';

    /**
     *
     */
    public const TABLE_LAP = 'lap';

    /**
     *
     */
    public const TABLE_ALIAS = 'be';

    /**
     *
     */
    public const USER_TABLE_ALIAS = 'u';

    /**
     *
     */
    public const LAP_TABLE_ALIAS = 'l';

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * BreathingExerciseRepository constructor.
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param string $id
     *
     * @return BreathingExercise
     * @throws Exception
     */
    public function find(string $id): BreathingExercise
    {
        $filter['limit'] = 1;
        $filter['id'] = $id;

        return $this->findOneBy($filter);
    }

    /**
     * @param array $filter
     *
     * @return BreathingExercise
     * @throws Exception
     */
    public function findOneBy(array $filter): BreathingExercise
    {
        $result = $this->getEmptyObject();
        $filter['limit'] = 1;

        $collection = $this->findBy($filter);

        if (!$collection->isEmpty()) {
            $result = $collection->first();
        }

        return $result;
    }

    /**
     * @return ArrayCollection
     * @throws Exception
     */
    public function findAll(): ArrayCollection
    {
        return $this->findBy([]);
    }

    /**
     *
     * @param array $filter
     *
     * @return ArrayCollection
     * @throws Exception
     * @throws \Exception
     */
    public function findBy(array $filter): ArrayCollection
    {
        $be = self::TABLE_ALIAS;
        $u = self::USER_TABLE_ALIAS;

        $qb = $this->entityManager->getConnection()->createQueryBuilder()
            ->from(self::TABLE, $be)
            ->select(
                "$be.id",
                "$be.session_number",
                "$be.duration",
                "$be.date_create",
                "$u.id as user_id",
                "$u.email",
                "$u.name"
            )
            ->leftJoin(
                self::TABLE_ALIAS,
                UserRepository::TABLE,
                self::USER_TABLE_ALIAS,
                "$u.id = $be.user_id"
            )->orderBy(
                "$be.session_number", 'desc'
            );

        $res = $this->filter($qb, $filter)
            ->executeQuery()->fetchAllAssociative();

        $collection = new ArrayCollection();

        foreach ($res as $r) {
            $collection->add($this->fromArray($r));
        }

        return $collection;
    }

    /**
     * @param string $id
     *
     * @return bool
     * @throws Exception
     */
    public function remove(string $id): bool
    {
        $filter['id'] = $id;

        return $this->removeBy($filter);
    }

    /**
     * @param array $filter
     *
     * @return bool
     * @throws Exception
     */
    public function removeBy(array $filter): bool
    {
        $result = false;

        $exercise = $this->findOneBy($filter);

        if (!$exercise->isEmpty()) {
            $connection = $this->entityManager->getConnection();

            // $conn instanceof Doctrine\DBAL\Connection
            $connection->beginTransaction(); // 0 => 1, "real" transaction started
            try {
                //logic
                $qb = $this->entityManager->getConnection()->createQueryBuilder();

                /** @var Lap $lap */
                foreach ($exercise->getLaps() as $lap) {
                    $qb->delete(self::TABLE_LAP)
                        ->where('id = :id')
                        ->setParameter('id', $lap->getUuid()->getUlid())
                        ->executeStatement();
                }

                $qb->delete(self::TABLE)
                    ->where('id = :id')
                    ->setParameter('id', $exercise->getUuid()->getUlid())
                    ->executeStatement();

                $connection->commit(); // 1 => 0, "real" transaction committed
                $result = true;
            } catch (\Exception $e) {
                $connection->rollBack(); // 1 => 0, "real" transaction rollback
                throw $e;
            }
        }

        return $result;
    }

    /**
     * @throws Exception
     */
    public function add(BreathingExercise $breathingExercise): BreathingExercise
    {
        $connection = $this->entityManager->getConnection();

        // $conn instanceof Doctrine\DBAL\Connection
        $connection->beginTransaction(); // 0 => 1, "real" transaction started
        try {
            //logic
            $qb = $this->entityManager->getConnection()->createQueryBuilder();
            $dateCreate = (new DateTimeImmutable())->format('Y-m-d H:i:s');

            $qb->insert(self::TABLE)
                ->values([
                    'id'             => ':id',
                    'user_id'        => ':user_id',
                    'session_number' => ':session_number',
                    'duration'       => ':duration',
                    'date_create'    => ':date_create'
                ])
                ->setParameters([
                    'id'             => $breathingExercise->getUuid()->getUlid(),
                    'user_id'        => $breathingExercise->getUser()->getId(),
                    'session_number' => $breathingExercise->getSessionNumber(),
                    'duration'       => $breathingExercise->getDuration()->s,
                    'date_create'    => $dateCreate
                ])
                ->executeStatement();

            /** @var Lap $lap */
            foreach ($breathingExercise->getLaps() as $lap) {
                $qb->insert(self::TABLE_LAP)
                    ->values([
                        'id'                    => ':id',
                        'breathing_exercise_id' => ':be_id',
                        'number'                => ':number',
                        'date_create'           => ':date_create',
                        'breaths'               => ':breaths',
                        'exhale_hold'           => ':exhale_hold',
                        'inhale_hold'           => ':inhale_hold',
                        'time'                  => ':time',
                    ])
                    ->setParameters([
                        'id'          => $lap->getUuid()->getUlid(),
                        'be_id'       => $breathingExercise->getUuid()->getUlid(),
                        'number'      => $lap->getNumber(),
                        'date_create' => $dateCreate,
                        'breaths'     => $lap->getBreaths(),
                        'exhale_hold' => $lap->getExhaleHold(),
                        'inhale_hold' => $lap->getInhaleHold(),
                        'time'        => $lap->getLapTime()->s,
                    ])
                    ->executeStatement();
            }

            $connection->commit(); // 1 => 0, "real" transaction committed
        } catch (\Exception $e) {
            $connection->rollBack(); // 1 => 0, "real" transaction rollback
            throw $e;
        }

        return $breathingExercise;
    }

    /**
     * @param UserInterface $user
     *
     * @return int
     * @throws Exception
     */
    public function countUserExercise(UserInterface $user): int
    {
        return $this->findBy(
            [
                'user_id' => $user->getId()
            ]
        )->count();
    }

    public function getEmptyObject(): BreathingExercise
    {
        return new BreathingExercise(
            new Ulid(0),
            new User(),
            new DateTimeImmutable()
        );
    }

    /**
     *
     * @param QueryBuilder $qb
     * @param array        $filter
     *
     * @return QueryBuilder
     *
     * @throws \Exception
     */
    private function filter(QueryBuilder $qb, array $filter): QueryBuilder
    {
        $be = self::TABLE_ALIAS;

        $qb->where('1 = 1');

        $filter = array_map(
            static function ($item) {
                if (!is_array($item)) {
                    $item = trim((string)$item);
                }

                return $item;
            },
            $filter
        );

        $filter = array_filter($filter);

        if (array_key_exists('limit', $filter)) {
            $qb->setMaxResults((int)$filter['limit']);
        }

        if (array_key_exists('id', $filter)) {
            if (is_array($filter['id'])) {
                $qb->andWhere($qb->expr()->in("$be.id", ':id'))
                    ->setParameter('id', $filter['id'], Connection::PARAM_STR_ARRAY);
            } else {
                $qb->andWhere("$be.id = :id")
                    ->setParameter('id', $filter['id']);
            }
        }

        if (array_key_exists('user_id', $filter)) {
            $qb->andWhere('user_id = :user_id')
                ->setParameter('user_id', $filter['user_id']);
        }

        if (array_key_exists('session_number', $filter)) {
            $qb->andWhere('session_number = :session_number')
                ->setParameter('session_number', $filter['session_number']);
        }

        if (array_key_exists('duration', $filter)) {
            $qb->andWhere('duration = :duration')
                ->setParameter('duration', $filter['duration']);
        }

        if (array_key_exists('date_create', $filter)) {
            $qb->andWhere('date_create <= :date_create')
                ->setParameter(
                    'date_create',
                    new DateTimeImmutable($filter['date_create']),
                    Types::DATETIME_IMMUTABLE
                );
        }

        return $qb;
    }

    /**
     * @throws \Exception
     */
    private function fromArray(array $data): BreathingExercise
    {
        $exercise = new BreathingExercise(
            new Ulid($data['id']),
            (new User())
                ->setId((int)$data['user_id'])
                ->setEmail((string)$data['email'])
                ->setName((string)$data['name']),
            new DateTimeImmutable((string)$data['date_create'])
        );
        $exercise->setSessionNumber((int)$data['session_number']);

        $this->loadLaps($exercise);

        return $exercise;
    }

    /**
     * @throws \Exception
     */
    private function loadLaps(BreathingExercise $breathingExercise): BreathingExercise
    {
        $l = self::LAP_TABLE_ALIAS;

        $qb = $this->entityManager->getConnection()->createQueryBuilder()
            ->select(
                "$l.id",
                "$l.breathing_exercise_id",
                "$l.number",
                "$l.breaths",
                "$l.exhale_hold",
                "$l.inhale_hold",
                "$l.time",
                "$l.date_create",
            )
            ->from(self::TABLE_LAP, $l)
            ->where('1 = 1')
            ->andWhere("$l.breathing_exercise_id = :bid")
            ->setParameter('bid', $breathingExercise->getUuid()->getUlid())
            ->orderBy("$l.number", 'asc');

        $res = $qb->executeQuery()->fetchAllAssociative();

        foreach ($res as $l) {
            $breathingExercise->addLap($this->lapFromArray($l));
        }

        return $breathingExercise;
    }

    /**
     * @throws \Exception
     */
    private function lapFromArray(array $data): Lap
    {
        $lapSet = new LapSet(
            (int)$data['breaths'],
            (int)$data['exhale_hold'],
            (int)$data['inhale_hold']
        );

        $lap = new Lap(
            new Ulid($data['id']),
            (int)$data['number'],
            $lapSet
        );

        $lap->setDateCreate(new DateTimeImmutable($data['date_create']));

        return $lap;
    }
}