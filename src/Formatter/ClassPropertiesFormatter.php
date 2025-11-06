<?php

declare(strict_types=1);

namespace League\Tactician\Logger\Formatter;

use League\Tactician\Logger\PropertyNormalizer\PropertyNormalizer;
use League\Tactician\Logger\PropertyNormalizer\SimplePropertyNormalizer;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Throwable;

/**
 * Formatter that includes the Command's name and properties for more detail
 */
class ClassPropertiesFormatter implements Formatter
{
    private PropertyNormalizer $normalizer;

    public function __construct(
        PropertyNormalizer|null $normalizer = null,
        private string $commandReceivedLevel = LogLevel::DEBUG,
        private string $commandSucceededLevel = LogLevel::DEBUG,
        private string $commandFailedLevel = LogLevel::ERROR,
    ) {
        $this->normalizer = $normalizer ?: new SimplePropertyNormalizer();
    }

    public function logCommandReceived(LoggerInterface $logger, object $command): void
    {
        $logger->log(
            $this->commandReceivedLevel,
            'Command received: ' . $command::class,
            ['command' => $this->normalizer->normalize($command)],
        );
    }

    /**
     * {@inheritDoc}
     */
    public function logCommandSucceeded(LoggerInterface $logger, object $command, $returnValue): void
    {
        $logger->log(
            $this->commandSucceededLevel,
            'Command succeeded: ' . $command::class,
            [
                'command' => $this->normalizer->normalize($command),
            ],
        );
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
