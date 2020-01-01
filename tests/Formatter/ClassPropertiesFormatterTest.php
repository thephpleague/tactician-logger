<?php

declare(strict_types=1);

namespace League\Tactician\Logger\Tests\Formatter;

use League\Tactician\Logger\Formatter\ClassPropertiesFormatter;
use League\Tactician\Logger\PropertyNormalizer\PropertyNormalizer;
use League\Tactician\Logger\Tests\Fixtures\RegisterUserCommand;
use League\Tactician\Logger\Tests\Fixtures\UserAlreadyExistsException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class ClassPropertiesFormatterTest extends TestCase
{
    /** @var PropertyNormalizer|MockObject */
    private $normalizer;

    /** @var ClassPropertiesFormatter */
    protected $formatter;

    /** @var LoggerInterface|MockObject */
    protected $logger;

    protected function setUp(): void
    {
        $this->normalizer = $this->createMock(PropertyNormalizer::class);
        $this->normalizer->method('normalize')->willReturn(['test' => 'data']);

        $this->logger = $this->createMock(LoggerInterface::class);

        $this->formatter = new ClassPropertiesFormatter($this->normalizer);
    }

    public function testBasicSuccessMessageIsLogged(): void
    {
        $this->logger
            ->expects(self::once())
            ->method('log')
            ->with(
                LogLevel::DEBUG,
                'Command succeeded: ' . RegisterUserCommand::class,
                ['command' => ['test' => 'data']]
            );

        $this->formatter->logCommandSucceeded($this->logger, new RegisterUserCommand(), null);
    }

    public function testCommandReceivedCreatesExpectedMessage(): void
    {
        $this->logger
            ->expects(self::once())
            ->method('log')
            ->with(
                LogLevel::DEBUG,
                'Command received: ' . RegisterUserCommand::class,
                ['command' => ['test' => 'data']]
            );

        $this->formatter->logCommandReceived($this->logger, new RegisterUserCommand());
    }

    public function testCommandFailedCreatesExpectedMessage(): void
    {
        $exception = new UserAlreadyExistsException();

        $this->logger
            ->expects(self::once())
            ->method('log')
            ->with(
                LogLevel::ERROR,
                'Command failed: ' . RegisterUserCommand::class,
                ['exception' => $exception]
            );

        $this->formatter->logCommandFailed($this->logger, new RegisterUserCommand(), $exception);
    }

    public function testCustomReceivedLogLevels(): void
    {
        $formatter = new ClassPropertiesFormatter(
            $this->normalizer,
            LogLevel::WARNING,
            LogLevel::DEBUG,
            LogLevel::DEBUG
        );

        $this->logger->expects(self::once())->method('log')->with(LogLevel::WARNING);
        $formatter->logCommandReceived($this->logger, new RegisterUserCommand());
    }

    public function testCustomSuccessLogLevels(): void
    {
        $formatter = new ClassPropertiesFormatter(
            $this->normalizer,
            LogLevel::DEBUG,
            LogLevel::NOTICE,
            LogLevel::DEBUG
        );

        $this->logger->expects(self::once())->method('log')->with(LogLevel::NOTICE);
        $formatter->logCommandSucceeded($this->logger, new RegisterUserCommand(), null);
    }

    public function testCustomFailureLogLevels(): void
    {
        $formatter = new ClassPropertiesFormatter(
            $this->normalizer,
            LogLevel::DEBUG,
            LogLevel::DEBUG,
            LogLevel::EMERGENCY
        );

        $this->logger->expects(self::once())->method('log')->with(LogLevel::EMERGENCY);
        $formatter->logCommandFailed($this->logger, new RegisterUserCommand(), new UserAlreadyExistsException());
    }
}
