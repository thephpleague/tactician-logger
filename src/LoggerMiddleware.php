<?php
namespace League\Tactician\Logger;

use League\Tactician\Logger\Formatter\Formatter;
use League\Tactician\Middleware;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
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
     * @var string
     */
    private $commandReceivedLogLevel;

    /**
     * @var string
     */
    private $commandHandledLogLevel;

    /**
     * @var string
     */
    private $commandFailedLogLevel;

    /**
     * @param Formatter $formatter
     * @param LoggerInterface $logger
     * @param string $commandReceivedLogLevel
     * @param string $commandHandledLogLevel
     * @param string $commandFailedLogLevel
     */
    public function __construct(
        Formatter $formatter,
        LoggerInterface $logger,
        $commandReceivedLogLevel = LogLevel::DEBUG,
        $commandHandledLogLevel = LogLevel::DEBUG,
        $commandFailedLogLevel = LogLevel::ERROR
    ) {
        $this->formatter = $formatter;
        $this->logger = $logger;
        $this->commandReceivedLogLevel = $commandReceivedLogLevel;
        $this->commandHandledLogLevel = $commandHandledLogLevel;
        $this->commandFailedLogLevel = $commandFailedLogLevel;
    }

    /**
     * {@inheritdoc}
     */
    public function execute($command, callable $next)
    {
        $this->log(
            $this->commandReceivedLogLevel,
            $this->formatter->commandReceived($command)
        );

        try {
            $returnValue = $next($command);
        } catch (Exception $e) {
            $this->log(
                $this->commandFailedLogLevel,
                $this->formatter->commandFailed($command, $e)
            );
            throw $e;
        }

        $this->log(
            $this->commandHandledLogLevel,
            $this->formatter->commandHandled($command)
        );
        return $returnValue;
    }

    /**
     * Write a message to the log or skip over it if the message is null
     *
     * @param string $logLevel
     * @param string|null $message
     */
    protected function log($logLevel, $message)
    {
        if ($message === null) {
            return;
        }

        $this->logger->log($logLevel, $message);
    }
}
