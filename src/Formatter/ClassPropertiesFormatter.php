<?php
namespace League\Tactician\Logger\Formatter;

use League\Tactician\Logger\PropertyNormalizer\PropertyNormalizer;
use League\Tactician\Logger\PropertyNormalizer\SimplePropertyNormalizer;
use Exception;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * Formatter that includes the Command's name and properties for more detail
 */
class ClassPropertiesFormatter implements Formatter
{
    /**
     * @var PropertyNormalizer
     */
    private $normalizer;

    /**
     * @var string
     */
    private $commandReceivedLevel;

    /**
     * @var string
     */
    private $commandSucceededLevel;

    /**
     * @var string
     */
    private $commandFailedLevel;

    /**
     * @param PropertyNormalizer $normalizer
     * @param string $commandReceivedLevel
     * @param string $commandSucceededLevel
     * @param string $commandFailedLevel
     */
    public function __construct(
        PropertyNormalizer $normalizer = null,
        $commandReceivedLevel = LogLevel::DEBUG,
        $commandSucceededLevel = LogLevel::DEBUG,
        $commandFailedLevel = LogLevel::ERROR
    ) {

        $this->normalizer = $normalizer ?: new SimplePropertyNormalizer();
        $this->commandReceivedLevel = $commandReceivedLevel;
        $this->commandSucceededLevel = $commandSucceededLevel;
        $this->commandFailedLevel = $commandFailedLevel;
    }

    /**
     * {@inheritDoc}
     */
    public function logCommandReceived(LoggerInterface $logger, $command)
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
    public function logCommandSucceeded(LoggerInterface $logger, $command, $returnValue)
    {
        $logger->log(
            $this->commandSucceededLevel,
            'Command succeeded: ' . get_class($command),
            [
                'command' => $this->normalizer->normalize($command)
            ]
        );
    }

    /**
     * {@inheritDoc}
     */
    public function logCommandFailed(LoggerInterface $logger, $command, Exception $e)
    {
        $logger->log(
            $this->commandFailedLevel,
            'Command failed: ' . get_class($command),
            ['exception' => $e]
        );
    }
}
