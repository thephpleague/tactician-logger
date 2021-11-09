<?php
namespace League\Tactician\Logger\Tests;

use League\Tactician\Logger\Formatter\Formatter;
use League\Tactician\Logger\LoggerMiddleware;
use League\Tactician\Logger\Tests\Fixtures\RegisterUserCommand;
use League\Tactician\Logger\Tests\Fixtures\UserAlreadyExistsException;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class LoggerMiddlewareTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var LoggerMiddleware
     */
    private $middleware;

    /**
     * @var Formatter|MockInterface
     */
    private $formatter;

    /**
     * @var callable
     */
    private $mockNext;

    protected function setUp(): void
    {
        $this->logger = Mockery::mock(LoggerInterface::class);
        $this->formatter = Mockery::mock(Formatter::class);

        $this->middleware = new LoggerMiddleware($this->formatter, $this->logger);
    }

    public function tearDown(): void
    {
        Mockery::close();
    }

    public function testSuccessfulEventsLogWithCommandAndReturnValue()
    {
        $command = new RegisterUserCommand();

        $this->formatter->shouldReceive('logCommandReceived')->with($this->logger, $command)->once();
        $this->formatter->shouldReceive('logCommandSucceeded')->with($this->logger, $command, 'blat bart')->once();

        $this->middleware->execute($command, function () {
            return 'blat bart';
        });
    }

    public function testEmptyReturnValuesIsPassedAsNull()
    {
        $command = new RegisterUserCommand();

        $this->formatter->shouldReceive('logCommandReceived')->with($this->logger, $command)->once();
        $this->formatter->shouldReceive('logCommandSucceeded')->with($this->logger, $command, null)->once();

        $this->middleware->execute(
            $command,
            function () {
                // no-op
            }
        );
    }

    public function testFailuresMessagesAreLoggedWithException()
    {
        $command = new RegisterUserCommand();
        $exception = new UserAlreadyExistsException();

        $this->formatter->shouldReceive('logCommandReceived')->with($this->logger, $command)->once();
        $this->formatter->shouldReceive('logCommandFailed')->with($this->logger, $command, $exception)->once();

        $this->expectException(UserAlreadyExistsException::class);

        $this->middleware->execute(
            $command,
            function () use ($exception) {
                throw $exception;
            }
        );
    }

    public function testNextCallableIsInvoked()
    {
        $this->logger->shouldIgnoreMissing();
        $this->formatter->shouldIgnoreMissing();

        $sentCommand = new RegisterUserCommand();
        $receivedSameCommand = false;
        $next = function ($receivedCommand) use (&$receivedSameCommand, $sentCommand) {
            $receivedSameCommand = ($receivedCommand === $sentCommand);
        };

        $this->middleware->execute($sentCommand, $next);

        $this->assertTrue($receivedSameCommand);
    }
}
