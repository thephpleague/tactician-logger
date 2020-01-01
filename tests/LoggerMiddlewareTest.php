<?php
declare(strict_types=1);

namespace League\Tactician\Logger\Tests;

use League\Tactician\Logger\Formatter\Formatter;
use League\Tactician\Logger\LoggerMiddleware;
use League\Tactician\Logger\Tests\Fixtures\RegisterUserCommand;
use League\Tactician\Logger\Tests\Fixtures\UserAlreadyExistsException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class LoggerMiddlewareTest extends TestCase
{
    /** @var LoggerInterface */
    private $logger;

    /** @var LoggerMiddleware */
    private $middleware;

    /** @var Formatter|MockObject */
    private $formatter;

    protected function setUp(): void
    {
        $this->logger = $this->createMock(LoggerInterface::class);
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

        $this->middleware->execute($command, function () {
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
            function () {
                // no-op
            }
        );
    }

    public function testFailuresMessagesAreLoggedWithException(): void
    {
        $command = new RegisterUserCommand();
        $exception = new UserAlreadyExistsException();

        $this->formatter
            ->expects(self::once())
            ->method('logCommandReceived')
            ->with($this->logger, $command);
        $this->formatter
            ->expects(self::once())
            ->method('logCommandFailed')
            ->with($this->logger, $command, $exception);

        $this->expectException(UserAlreadyExistsException::class);
        $this->middleware->execute(
            $command,
            function () use ($exception) {
                throw $exception;
            }
        );
    }

    public function testNextCallableIsInvoked(): void
    {
        $sentCommand = new RegisterUserCommand();
        $receivedSameCommand = false;
        $next = function ($receivedCommand) use (&$receivedSameCommand, $sentCommand) {
            $receivedSameCommand = ($receivedCommand === $sentCommand);
        };

        $this->middleware->execute($sentCommand, $next);

        $this->assertTrue($receivedSameCommand);
    }
}
