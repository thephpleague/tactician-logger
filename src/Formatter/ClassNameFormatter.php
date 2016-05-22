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
     * @return string|null
     */
    public function commandFailed($command)
    {
        return 'Command failed: ' . get_class($command);
    }

    /**
     * {@inheritDoc}
     */
    public function commandContext($command)
    {
        return ['class' => get_class($command)];
    }

    /**
     * {@inheritDoc}
     */
    public function failureContext(array $currentContext, \Exception $e)
    {
        return $currentContext + ['exception' => $e];
    }
}
