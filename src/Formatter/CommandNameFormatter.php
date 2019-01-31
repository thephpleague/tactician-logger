<?php
namespace League\Tactician\Logger\Formatter;

use Exception;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor;
use League\Tactician\Handler\CommandNameExtractor\CommandNameExtractor;

/**
 * Returns log messages only dump the Command name& Exception's class name.
 */
class CommandNameFormatter implements Formatter
{
    /**
     * @var CommandNameExtractor
     */
    private $commandNameExtractor;

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
        CommandNameExtractor $commandNameExtractor = null,
        $commandReceivedLevel = LogLevel::DEBUG,
        $commandSucceededLevel = LogLevel::DEBUG,
        $commandFailedLevel = LogLevel::ERROR
    ) {
        $this->commandNameExtractor = $commandNameExtractor ?: new ClassNameExtractor();
        $this->commandReceivedLevel = $commandReceivedLevel;
        $this->commandSucceededLevel = $commandSucceededLevel;
        $this->commandFailedLevel = $commandFailedLevel;
    }

    /**
     * Get the extracted command name
     *
     * @return string
     */
    private function getCommandName($command)
    {
        return $this->commandNameExtractor->extract($command);
    }

    /**
     * {@inheritDoc}
     */
    public function logCommandReceived(LoggerInterface $logger, $command)
    {
        $logger->log(
            $this->commandReceivedLevel, 
            'Command received: ' . $this->getCommandName($command), 
            []
        );
    }

    /**
     * {@inheritDoc}
     */
    public function logCommandSucceeded(LoggerInterface $logger, $command, $returnValue)
    {
        $logger->log(
            $this->commandSucceededLevel, 
            'Command succeeded: ' . $this->getCommandName($command),
            []
        );
    }

    /**
     * {@inheritDoc}
     */
    public function logCommandFailed(LoggerInterface $logger, $command, Exception $e)
    {
        $logger->log(
            $this->commandFailedLevel,
            'Command failed: ' . $this->getCommandName($command),
            ['exception' => $e]
        );
    }
}
