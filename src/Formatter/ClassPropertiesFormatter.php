<?php

declare(strict_types=1);

namespace League\Tactician\Logger\Formatter;

use League\Tactician\Logger\PropertyNormalizer\PropertyNormalizer;
use League\Tactician\Logger\PropertyNormalizer\SimplePropertyNormalizer;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Throwable;
use function get_class;

/**
 * Formatter that includes the Command's name and properties for more detail
 */
class ClassPropertiesFormatter implements Formatter
{
    /** @var PropertyNormalizer */
    private $normalizer;

    /** @var string */
    private $commandReceivedLevel;

    /** @var string */
    private $commandSucceededLevel;

    /** @var string */
    private $commandFailedLevel;

    public function __construct(
        ?PropertyNormalizer $normalizer = null,
        string $commandReceivedLevel = LogLevel::DEBUG,
        string $commandSucceededLevel = LogLevel::DEBUG,
        string $commandFailedLevel = LogLevel::ERROR
    ) {
        $this->normalizer            = $normalizer ?: new SimplePropertyNormalizer();
        $this->commandReceivedLevel  = $commandReceivedLevel;
        $this->commandSucceededLevel = $commandSucceededLevel;
        $this->commandFailedLevel    = $commandFailedLevel;
    }

    public function logCommandReceived(LoggerInterface $logger, object $command) : void
    {
        $logger->log(
            $this->commandReceivedLevel,
            'Command received: ' . get_class($command),
            ['command' => $this->normalizer->normalize($command)]
        );
    }

    /**
     * {@inheritDoc}
     */
    public function logCommandSucceeded(LoggerInterface $logger, object $command, $returnValue) : void
    {
        $logger->log(
            $this->commandSucceededLevel,
            'Command succeeded: ' . get_class($command),
            [
                'command' => $this->normalizer->normalize($command),
            ]
        );
    }

    public function logCommandFailed(LoggerInterface $logger, object $command, Throwable $e) : void
    {
        $logger->log(
            $this->commandFailedLevel,
            'Command failed: ' . get_class($command),
            ['exception' => $e]
        );
    }
}
