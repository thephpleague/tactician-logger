<?php
namespace League\Tactician\Logger\Tests;

use League\Tactician\Logger\Formatter\Formatter;
use League\Tactician\Logger\LoggerMiddleware;
use League\Tactician\Logger\Tests\Fixtures\RegisterUserCommand;
use League\Tactician\Logger\Tests\Fixtures\UserAlreadyExistsException;
use Mockery;
use Mockery\MockInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class LoggerMiddlewareTest extends \PHPUnit_Framework_TestCase
{
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

    protected function setUp()
    {
        $this->logger = Mockery::mock(LoggerInterface::class);
        $this->formatter = Mockery::mock(Formatter::class);

        $this->middleware = new LoggerMiddleware($this->formatter, $this->logger);
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

    /**
     * @expectedException \League\Tactician\Logger\Tests\Fixtures\UserAlreadyExistsException
     */
    public function testFailuresMessagesAreLoggedWithException()
    {
        $command = new RegisterUserCommand();
        $exception = new UserAlreadyExistsException();

        $this->formatter->shouldReceive('logCommandReceived')->with($this->logger, $command)->once();
        $this->formatter->shouldReceive('logCommandFailed')->with($this->logger, $command, $exception)->once();

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
