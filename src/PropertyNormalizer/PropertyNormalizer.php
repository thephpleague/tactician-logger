<?php
namespace League\Tactician\Logger\PropertyNormalizer;

/**
 * Normalize value into scalars, usually to put them in a log message's context
 *
 * If given an object, return an array of properties. If given scalars, just
 * return them directly.
 *
 * Implementations should work on any value, not just commands or exceptions.
 */
interface PropertyNormalizer
{
    /**
     * @param mixed $value
     * @return string
     */
    public function normalize($value);
}
