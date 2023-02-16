<?php

declare(strict_types=1);

namespace App\Service\Serializer;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 *
 */
class Deserializer
{
    /**
     *
     */
    public const JSON = 'json';

    /**
     *
     */
    public const ARRAY = 'array';

    private Serializer $serializer;

    public function __construct()
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $this->serializer = new Serializer($normalizers, $encoders);
    }

    public function jsonToObject(string $data, string $class)
    {
        $this->serializer->deserialize($data, $class, self::JSON);
    }

    public function jsonCollectionToObjectCollection(string $data, string $class) : ArrayCollection
    {
        $items = json_decode($data, true);

        $collection = new ArrayCollection();

        foreach ($items as $item) {
            $collection->add($this->serializer->deserialize(json_encode($item), $class, self::JSON));
        }

        return $collection;
    }
}