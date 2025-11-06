<?php

declare(strict_types=1);

namespace League\Tactician\Logger\PropertyNormalizer;

use ReflectionClass;

use function get_resource_type;
use function gettype;

/**
 * Quick'n'dirty property normalizer that logs the first level properties
 *
 * Does not recurse into sub-objects or arrays.
 *
 * This is done in an extremely inefficient manner, so please never use this in
 * a production context, only for local debugging.
 */
class SimplePropertyNormalizer implements PropertyNormalizer
{
    /**
     * {@inheritDoc}
     * @return array<string, mixed>
     */
    public function normalize(object $command): array
    {
        $reflectionClass = new ReflectionClass($command::class);

        $properties = [];
        foreach ($reflectionClass->getProperties() as $property) {
            $property->setAccessible(true);
            $properties[$property->getName()] = $this->formatValue($property->getValue($command));
        }

        return $properties;
    }

    /**
     * Return the given (property) value as a descriptive string
     *
     * @param mixed $value Can be literally anything
     */
    protected function formatValue(mixed $value): mixed
    {
        switch (gettype($value)) {
            case 'object':
                return 'object(' . $value::class . ')';

            case 'array':
                return '*array*';

            case 'resource':
                return 'resource(' . get_resource_type($value) . ')';

            default:
                return $value;
        }
    }
}
