<?php
namespace League\Tactician\Logger\Formatter;

use Exception;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * Returns log messages only dump the Command & Exception's class names.
 */
class ClassNameFormatter implements Formatter
{
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
     * @param string $commandReceivedLevel
     * @param string $commandSucceededLevel
     * @param string $commandFailedLevel
     */
    public function __construct(
        $commandReceivedLevel = LogLevel::DEBUG,
        $commandSucceededLevel = LogLevel::DEBUG,
        $commandFailedLevel = LogLevel::ERROR
    ) {
        $this->commandReceivedLevel = $commandReceivedLevel;
        $this->commandSucceededLevel = $commandSucceededLevel;
        $this->commandFailedLevel = $commandFailedLevel;
    }

    /**
     * {@inheritDoc}
     */
    public function logCommandReceived(LoggerInterface $logger, $command)
    {
        $logger->log($this->commandReceivedLevel, 'Command received: ' . get_class($command), []);
    }

    /**
     * {@inheritDoc}
     */
    public function logCommandSucceeded(LoggerInterface $logger, $command, $returnValue)
    {
        $logger->log($this->commandSucceededLevel, 'Command succeeded: ' . get_class($command), []);
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
