<?php

declare(strict_types=1);

namespace League\Tactician\Logger;

use League\Tactician\Logger\Formatter\Formatter;
use League\Tactician\Middleware;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * Add support for writing a message to the log whenever a command is received,
 * handled or failed.
 */
class LoggerMiddleware implements Middleware
{
    public function __construct(
        private Formatter $formatter,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function execute(object $command, callable $next): mixed
    {
        $this->formatter->logCommandReceived($this->logger, $command);

        try {
            $returnValue = $next($command);
        } catch (Throwable $e) {
            $this->formatter->logCommandFailed($this->logger, $command, $e);

            throw $e;
        }

        $this->formatter->logCommandSucceeded($this->logger, $command, $returnValue);

        return $returnValue;
    }
}
