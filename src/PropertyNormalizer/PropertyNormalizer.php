<?php
namespace League\Tactician\Logger\PropertyNormalizer;

/**
 * Normalize an object's property values into a format fit for a log message.
 */
interface PropertyNormalizer
{
    /**
     * @param object $command
     * @return string
     */
    public function normalize($command);
}
