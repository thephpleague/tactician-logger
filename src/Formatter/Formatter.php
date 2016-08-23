<?php
namespace League\Tactician\Logger\Formatter;

use Exception;
use Psr\Log\LoggerInterface;

/**
 * Converts incoming Commands into log messages.
 *
 * Each method is written for a particular command path. A formatter class
 * should take the given command, format it to a message and pass it to the
 * given logger (with the desired log level).
 *
 * For an example of what this all looks like, take a look at the
 * ClassNameFormatter example bundled with this package.
 *
 * A formatter may also use PSR-3 log contexts to pass extra info to the logger
 * about the commands, return values and errors it receives. For more
 * information about log contexts, see the PSR-3 specification.
 * @see https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md#13-context
 */
interface Formatter
{
    /**
     * @param LoggerInterface $logger
     * @param object $command
     * @return void
     */
    public function logCommandReceived(LoggerInterface $logger, $command);

    /**
     * @param LoggerInterface $logger
     * @param object $command
     * @param mixed $returnValue
     * @return void
     */
    public function logCommandSucceeded(LoggerInterface $logger, $command, $returnValue);

    /**
     * @param LoggerInterface $logger
     * @param object $command
     * @param Exception $e
     * @return void
     */
    public function logCommandFailed(LoggerInterface $logger, $command, Exception $e);
}
