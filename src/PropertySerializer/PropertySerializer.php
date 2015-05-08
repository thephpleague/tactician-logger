<?php
namespace League\Tactician\Logger\PropertySerializer;

/**
 * Encode an object's property values into a format fit for a log message.
 */
interface PropertySerializer
{
    /**
     * @param object $command
     * @return string
     */
    public function encode($command);
}
