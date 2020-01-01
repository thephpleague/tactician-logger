<?php
declare(strict_types=1);

namespace League\Tactician\Logger\Tests\Formatter;

use League\Tactician\Logger\Formatter\ClassNameFormatter;
use League\Tactician\Logger\Tests\Fixtures\RegisterUserCommand;
use League\Tactician\Logger\Tests\Fixtures\UserAlreadyExistsException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class ClassNameFormatterTest extends TestCase
{
    /** @var ClassNameFormatter */
    protected $formatter;

    /** @var LoggerInterface|MockObject */
    protected $logger;

    protected function setUp(): void
    {
        $this->formatter = new ClassNameFormatter();
        $this->logger = $this->createMock(LoggerInterface::class);
    }

    public function testBasicSuccessMessageIsLogged(): void
    {
        $this->logger->expects(self::once())->method('log')->with(
            LogLevel::DEBUG,
            'Command succeeded: ' . RegisterUserCommand::class,
            []
        );

        $this->formatter->logCommandSucceeded($this->logger, new RegisterUserCommand(), null);
    }

    public function testCommandReceivedCreatesExpectedMessage(): void
    {
        $this->logger->expects(self::once())->method('log')->with(
            LogLevel::DEBUG,
            'Command received: ' . RegisterUserCommand::class,
            []
        );

        $this->formatter->logCommandReceived($this->logger, new RegisterUserCommand());
    }

    public function testCommandFailedCreatesExpectedMessage(): void
    {
        $exception = new UserAlreadyExistsException();

        $this->logger->expects(self::once())->method('log')->with(
            LogLevel::ERROR,
            'Command failed: ' . RegisterUserCommand::class,
            ['exception' => $exception]
        );

        $this->formatter->logCommandFailed($this->logger, new RegisterUserCommand(), $exception);
    }

    public function testCustomReceivedLogLevel(): void
    {
        $formatter = new ClassNameFormatter(LogLevel::WARNING, LogLevel::DEBUG, LogLevel::DEBUG);

        $this->logger
            ->expects(self::once())
            ->method('log')
            ->with(
                LogLevel::WARNING,
                'Command received: League\Tactician\Logger\Tests\Fixtures\RegisterUserCommand'
            );

        $formatter->logCommandReceived($this->logger, new RegisterUserCommand());
    }

    public function testCustomSuccessLogLevel(): void
    {
        $formatter = new ClassNameFormatter(LogLevel::DEBUG, LogLevel::NOTICE, LogLevel::DEBUG);

        $this->logger
            ->expects(self::once())
            ->method('log')
            ->with(
                LogLevel::NOTICE,
                'Command succeeded: League\Tactician\Logger\Tests\Fixtures\RegisterUserCommand'
            );
        $formatter->logCommandSucceeded($this->logger, new RegisterUserCommand(), null);
    }

    public function testCustomErrorLogLevel(): void
    {
        $formatter = new ClassNameFormatter(LogLevel::DEBUG, LogLevel::DEBUG, LogLevel::EMERGENCY);

        $this->logger
            ->expects(self::once())
            ->method('log')
            ->with(LogLevel::EMERGENCY);
        $formatter->logCommandFailed($this->logger, new RegisterUserCommand(), new UserAlreadyExistsException());
    }
}
