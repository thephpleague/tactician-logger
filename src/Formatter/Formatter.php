<?php
namespace League\Tactician\Logger\Formatter;

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
     * @param object $command
     * @return string|null
     */
    public function commandReceived($command);

    /**
     * @param object $command
     * @return string|null
     */
    public function commandHandled($command);

    /**
     * @param object $command
     * @param Exception $e
     * @return string|null
     */
    public function commandFailed($command, Exception $e);

    /**
     * @param object $command
     * @return array
     */
    public function commandContext($command);
}
