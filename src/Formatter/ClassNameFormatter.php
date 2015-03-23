<?php
namespace League\Tactician\Logger\Formatter;

use League\Tactician\Command;
use Exception;

/**
 * Returns log messages only dump the Command & Exception's class names.
 */
class ClassNameFormatter implements Formatter
{
    /**
     * @param Command $command
     * @return string|null
     */
    public function commandReceived(Command $command)
    {
        return 'Command received: ' . get_class($command);
    }

    /**
     * @param Command $command
     * @return string|null
     */
    public function commandHandled(Command $command)
    {
        return 'Command succeeded: ' . get_class($command);
    }

    /**
     * @param Command $command
     * @param Exception $e
     * @return string|null
     */
    public function commandFailed(Command $command, Exception $e)
    {
        $commandClass = get_class($command);
        $exceptionClass = get_class($e);
        $exceptionMessage = $e->getMessage();

        return "Command failed: {$commandClass} threw the exception {$exceptionClass} ({$exceptionMessage})";
    }
}
