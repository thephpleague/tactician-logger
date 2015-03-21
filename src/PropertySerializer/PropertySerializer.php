<?php
namespace League\Tactician\Logger\PropertySerializer;

use League\Tactician\Command;

/**
 * Encode a command's property values into a format fit for a log message.
 */
interface PropertySerializer
{
    /**
     * @param Command $command
     * @return string
     */
    public function encode(Command $command);
}
