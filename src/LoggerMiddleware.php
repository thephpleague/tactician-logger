<?php
namespace League\Tactician\Logger;

use League\Tactician\Command;
use League\Tactician\Logger\Formatter\Formatter;
use League\Tactician\Middleware;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Exception;

/**
 *
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
    private $commandCompletedLogLevel;

    /**
     * @var string
     */
    private $commandFailedLogLevel;

    /**
     * @param Formatter $formatter
     * @param LoggerInterface $logger
     * @param string $commandReceivedLogLevel
     * @param string $commandCompletedLogLevel
     * @param string $commandFailedLogLevel
     */
    public function __construct(
        Formatter $formatter,
        LoggerInterface $logger,
        $commandReceivedLogLevel = LogLevel::DEBUG,
        $commandCompletedLogLevel = LogLevel::DEBUG,
        $commandFailedLogLevel = LogLevel::ERROR
    ) {
        $this->formatter = $formatter;
        $this->logger = $logger;
        $this->commandReceivedLogLevel = $commandReceivedLogLevel;
        $this->commandCompletedLogLevel = $commandCompletedLogLevel;
        $this->commandFailedLogLevel = $commandFailedLogLevel;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Command $command, callable $next)
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
            $this->commandCompletedLogLevel,
            $this->formatter->commandCompleted($command)
        );
        return $returnValue;
    }

    /**
     * @param string $logLevel
     * @param string $message
     */
    protected function log($logLevel, $message)
    {
        if ($message === null) {
            return;
        }

        $this->logger->log($logLevel, $message);
    }
}
