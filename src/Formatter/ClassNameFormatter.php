<?php

declare(strict_types=1);

namespace League\Tactician\Logger\Formatter;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Throwable;

/**
 * Returns log messages only dump the Command & Exception's class names.
 */
class ClassNameFormatter implements Formatter
{
    public function __construct(
        private string $commandReceivedLevel = LogLevel::DEBUG,
        private string $commandSucceededLevel = LogLevel::DEBUG,
        private string $commandFailedLevel = LogLevel::ERROR,
    ) {
    }

    public function logCommandReceived(LoggerInterface $logger, object $command): void
    {
        $logger->log($this->commandReceivedLevel, 'Command received: ' . $command::class, []);
    }

    public function logCommandSucceeded(LoggerInterface $logger, object $command, mixed $returnValue): void
    {
        $logger->log($this->commandSucceededLevel, 'Command succeeded: ' . $command::class, []);
    }

    public function logCommandFailed(LoggerInterface $logger, object $command, Throwable $e): void
    {
        $logger->log(
            $this->commandFailedLevel,
            'Command failed: ' . $command::class,
            ['exception' => $e],
        );
    }
}
