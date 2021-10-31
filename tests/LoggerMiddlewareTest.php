<?php

declare(strict_types=1);

namespace League\Tactician\Logger\Tests;

use League\Tactician\Logger\Formatter\Formatter;
use League\Tactician\Logger\LoggerMiddleware;
use League\Tactician\Logger\Tests\Fixtures\RegisterUserCommand;
use League\Tactician\Logger\Tests\Fixtures\UserAlreadyExists;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class LoggerMiddlewareTest extends TestCase
{
    private LoggerInterface $logger;

    private LoggerMiddleware $middleware;

    /** @var Formatter|MockObject */
    private $formatter;

    protected function setUp(): void
    {
        $this->logger    = $this->createMock(LoggerInterface::class);
        $this->formatter = $this->createMock(Formatter::class);

        $this->middleware = new LoggerMiddleware($this->formatter, $this->logger);
    }

    public function testSuccessfulEventsLogWithCommandAndReturnValue(): void
    {
        $command = new RegisterUserCommand();

        $this->formatter
            ->expects(self::once())
            ->method('logCommandReceived')
            ->with($this->logger, $command);

        $this->formatter
            ->expects(self::once())
            ->method('logCommandSucceeded')
            ->with($this->logger, $command, 'blat bart');

        $this->middleware->execute($command, static function () {
            return 'blat bart';
        });
    }

    public function testEmptyReturnValuesIsPassedAsNull(): void
    {
        $command = new RegisterUserCommand();

        $this->formatter
            ->expects(self::once())
            ->method('logCommandReceived')
            ->with($this->logger, $command);

        $this->formatter
            ->expects(self::once())
            ->method('logCommandSucceeded')
            ->with($this->logger, $command, null);

        $this->middleware->execute(
            $command,
            static function (): void {
                // no-op
            }
        );
    }

    public function testFailuresMessagesAreLoggedWithException(): void
    {
        $command   = new RegisterUserCommand();
        $exception = new UserAlreadyExists();

        $this->formatter
            ->expects(self::once())
            ->method('logCommandReceived')
            ->with($this->logger, $command);
        $this->formatter
            ->expects(self::once())
            ->method('logCommandFailed')
            ->with($this->logger, $command, $exception);

        $this->expectException(UserAlreadyExists::class);
        $this->middleware->execute(
            $command,
            static function () use ($exception): void {
                throw $exception;
            }
        );
    }

    public function testNextCallableIsInvoked(): void
    {
        $sentCommand         = new RegisterUserCommand();
        $receivedSameCommand = false;
        $next                = static function ($receivedCommand) use (&$receivedSameCommand, $sentCommand): void {
            $receivedSameCommand = ($receivedCommand === $sentCommand);
        };

        $this->middleware->execute($sentCommand, $next);

        $this->assertTrue($receivedSameCommand);
    }
}
