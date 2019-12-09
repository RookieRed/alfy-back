<?php

namespace App\Utils;

use DateTimeZone;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ConstraintViolationListNormalizer;
use Symfony\Component\Serializer\Normalizer\DataUriNormalizer;
use Symfony\Component\Serializer\Normalizer\DateIntervalNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\JsonSerializableNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class JsonSerializer extends Serializer
{


    public function __construct(
        ClassMetadataFactoryInterface $classMetadataFactory,
        PropertyAccessorInterface $propertyAccessor)
    {
        $defaultContext = [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $context) {
                return $object->getId();
            },
        ];
        $objectNormalizer = new ObjectNormalizer(
            $classMetadataFactory,
            null,
            $propertyAccessor,
            new ReflectionExtractor(),
            null,
            null,
            $defaultContext
        );
        parent::__construct(
            [
                new DateTimeNormalizer('Y-m-d H:i:s', new DateTimeZone(('Europe/Amsterdam'))),
                new DateIntervalNormalizer(),
                new JsonSerializableNormalizer($classMetadataFactory),
                new ConstraintViolationListNormalizer(),
                new DataUriNormalizer(),
                new ArrayDenormalizer(),
                $objectNormalizer
            ],
            [
                new JsonEncoder()
            ]
        );
    }

    public function jsonSerialize($data, $groups = [])
    {
        $config = $groups === [] ? [] : ['groups' => $groups];
        return $this->serialize($data, 'json', $config);
    }

    public function jsonDeserialize($jsonData, $type, $groups = [])
    {
        $config = $groups === [] ? [] : ['groups' => $groups];
        return $this->deserialize($jsonData, $type, 'json', $config);
    }
}
