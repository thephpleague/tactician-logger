<?php

declare(strict_types=1);

namespace League\Tactician\Logger\Formatter;

use Exception;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * Returns log messages only dump the Command & Exception's class names.
 */
class ClassNameFormatter implements Formatter
{
    /** @var string */
    private $commandReceivedLevel;

    /** @var string */
    private $commandSucceededLevel;

    /** @var string */
    private $commandFailedLevel;

    public function __construct(
        string $commandReceivedLevel = LogLevel::DEBUG,
        string $commandSucceededLevel = LogLevel::DEBUG,
        string $commandFailedLevel = LogLevel::ERROR
    ) {
        $this->commandReceivedLevel = $commandReceivedLevel;
        $this->commandSucceededLevel = $commandSucceededLevel;
        $this->commandFailedLevel = $commandFailedLevel;
    }

    /**
     * {@inheritDoc}
     */
    public function logCommandReceived(LoggerInterface $logger, object $command): void
    {
        $logger->log($this->commandReceivedLevel, 'Command received: ' . get_class($command), []);
    }

    /**
     * {@inheritDoc}
     */
    public function logCommandSucceeded(LoggerInterface $logger, object $command, $returnValue): void
    {
        $logger->log($this->commandSucceededLevel, 'Command succeeded: ' . get_class($command), []);
    }

    /**
     * {@inheritDoc}
     */
    public function logCommandFailed(LoggerInterface $logger, object $command, Exception $e): void
    {
        $logger->log(
            $this->commandFailedLevel,
            'Command failed: ' . get_class($command),
            ['exception' => $e]
        );
    }
}
