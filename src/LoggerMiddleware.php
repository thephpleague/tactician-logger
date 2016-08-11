<?php
namespace League\Tactician\Logger;

use League\Tactician\Logger\Formatter\Formatter;
use League\Tactician\Middleware;
use Psr\Log\LoggerInterface;
use Exception;

/**
 * Add support for writing a message to the log whenever a command is received,
 * handled or failed.
 */
class LoggerMiddleware implements Middleware
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Formatter
     */
    private $formatter;

    /**
     * @param Formatter $formatter
     * @param LoggerInterface $logger
     */
    public function __construct(Formatter $formatter, LoggerInterface $logger)
    {
        $this->formatter = $formatter;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function execute($command, callable $next)
    {
        $this->formatter->logCommandReceived($this->logger, $command);

        try {
            $returnValue = $next($command);
        } catch (Exception $e) {
            $this->formatter->logCommandFailed($this->logger, $command, $e);
            throw $e;
        }

        $this->formatter->logCommandSucceeded($this->logger, $command, $returnValue);

        return $returnValue;
    }
}
