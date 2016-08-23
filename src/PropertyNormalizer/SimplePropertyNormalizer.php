<?php
namespace League\Tactician\Logger\PropertyNormalizer;

use ReflectionClass;

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
     * @param object $command
     * @return array
     */
    public function normalize($command)
    {
        $reflectionClass = new ReflectionClass(get_class($command));

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
     * @return string
     */
    protected function formatValue($value)
    {
        switch (gettype($value)) {
            case 'object':
                return 'object(' . get_class($value) . ')';
            case 'array':
                return '*array*';
            case 'resource':
                return 'resource(' . get_resource_type($value) . ')';
            default:
                return $value;
        }
    }
}
