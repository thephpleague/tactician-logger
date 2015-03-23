<?php
namespace League\Tactician\Logger\Formatter;

use League\Tactician\Command;
use Exception;

/**
 * Converts incoming Commands into log messages.
 *
 * Each method is invoked for a specific use case and can return either string
 * or null. Any string will be written to the logger, nulls will be ignored and
 * write nothing to the log.
 *
 * commandReceived() and commandHandled() receive only the command, however
 * commandFailed() also receives the exception that caused the failure.
 */
interface Formatter
{
    /**
     * @param Command $command
     * @return string|null
     */
    public function commandReceived(Command $command);

    /**
     * @param Command $command
     * @return string|null
     */
    public function commandHandled(Command $command);

    /**
     * @param Command $command
     * @param Exception $e
     * @return string|null
     */
    public function commandFailed(Command $command, Exception $e);
}
