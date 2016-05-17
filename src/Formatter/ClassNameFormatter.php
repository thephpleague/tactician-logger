<?php
namespace League\Tactician\Logger\Formatter;

use Exception;

/**
 * Returns log messages only dump the Command & Exception's class names.
 */
class ClassNameFormatter implements Formatter
{
    /**
     * @param object $command
     * @return string|null
     */
    public function commandReceived($command)
    {
        return 'Command received: ' . get_class($command);
    }

    /**
     * @param object $command
     * @return string|null
     */
    public function commandHandled($command)
    {
        return 'Command succeeded: ' . get_class($command);
    }

    /**
     * @param object $command
     * @param Exception $e
     * @return string|null
     */
    public function commandFailed($command, Exception $e)
    {
        $commandClass = get_class($command);
        $exceptionClass = get_class($e);
        $exceptionMessage = $e->getMessage();

        return "Command failed: {$commandClass} threw the exception {$exceptionClass} ({$exceptionMessage})";
    }

    /**
     * {@inheritDoc}
     */
    public function commandContext($command)
    {
        return [];
    }
}
