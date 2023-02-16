<?php

declare(strict_types=1);

namespace App\Service\Serializer\Wim;

use App\Entity\Wim\Domain\Aggregate\BreathingExercise;
use App\Service\DateConverter;
use DateTime;
use Doctrine\Common\Collections\Collection;
use Exception;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DateIntervalNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 *
 */
class BreathingExerciseDeserializer
{
    /**
     *
     */
    public const JSON = 'json';

    /**
     *
     */
    public const ARRAY = 'array';

    /**
     * @param BreathingExercise $breathingExercise
     * @param string            $format
     *
     * @return array
     * @throws ExceptionInterface
     * @throws Exception
     */
    public static function deserialize(BreathingExercise $breathingExercise, string $format = 'array')
    {
        $data = self::getBreathingExerciseNormalizeData($breathingExercise);

        $data = self::adaptToFront($data);

        $result = ($format === self::JSON) ? json_encode($data) : $data;

        return $result;
    }

    /**
     * @param Collection $breathingExercises
     * @param string     $format
     *
     * @return array
     * @throws Exception
     * @throws ExceptionInterface
     */
    public static function deserializeCollection(Collection $breathingExercises, string $format = 'array')
    {
        $data = self::getBreathingExerciseNormalizeData($breathingExercises);

        foreach ($data as $i => $d) {
            $data[$i] = self::adaptToFront($d);
        }

        $result = ($format === self::JSON) ? json_encode($data) : $data;

        return $result;
    }

    /**
     * @param $breathingExercise
     *
     * @return mixed
     * @throws ExceptionInterface
     */
    public static function getBreathingExerciseNormalizeData($breathingExercise): mixed
    {
        $serializer = new Serializer(
            [
                new DateIntervalNormalizer(['dateinterval_format' => '%s']),
                new DateTimeNormalizer(['datetime_format' => 'd.m.Y']),
                new ObjectNormalizer()
            ]
        );

        $data = $serializer->normalize(
            $breathingExercise,
            null,
            [
                AbstractNormalizer::ATTRIBUTES =>
                    [
                        'sessionNumber',
                        'dateCreate',
                        'user' => ['id'],
                        'laps' => ['number', 'set'],
                        'uuid' => ['ulid'],
                        'duration',
                        'maxExhaleHold',
                        'maxBreaths'
                    ]
            ]
        );

        return $data;
    }

    /**
     * @param mixed $data
     *
     * @return mixed
     * @throws Exception
     */
    public static function adaptToFront(mixed $data): mixed
    {
        $data['uuid'] = $data['uuid']['ulid'];
        $data['duration'] = DateConverter::secToStr((int)$data['duration']);
        foreach ($data['laps'] as $li => $lap) {
            $data['laps'][$li]['set']['lapTime'] = DateConverter::secToStr((int)$lap['set']['lapTime']);
        }

        return $data;
    }
}