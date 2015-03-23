<?php
namespace League\Tactician\Logger\Tests;

use League\Tactician\Logger\Formatter\Formatter;
use League\Tactician\Logger\LoggerMiddleware;
use League\Tactician\Logger\Tests\Fixtures\RegisterUserCommand;
use League\Tactician\Logger\Tests\Fixtures\UserAlreadyExistsException;
use Psr\Log\LoggerInterface;
use Mockery;
use Mockery\MockInterface;
use Psr\Log\LogLevel;

class LoggerMiddlewareTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LoggerMiddleware
     */
    private $middleware;

    /**
     * @var LoggerInterface|MockInterface
     */
    private $logger;

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

        $this->middleware = new LoggerMiddleware(
            $this->formatter,
            $this->logger
        );

        $this->mockNext = function () {
        };
    }

    public function testSuccessMessagesAreLoggedExactly()
    {
        $command = new RegisterUserCommand();

        $this->formatter->shouldReceive('commandReceived')->with($command)->once()->andReturn('foo bar');
        $this->formatter->shouldReceive('commandHandled')->with($command)->once()->andReturn('baz blat');

        $this->logger->shouldReceive('log')->with(LogLevel::DEBUG, 'foo bar')->once();
        $this->logger->shouldReceive('log')->with(LogLevel::DEBUG, 'baz blat')->once();

        $this->middleware->execute($command, $this->mockNext);
    }

    /**
     * @expectedException \League\Tactician\Logger\Tests\Fixtures\UserAlreadyExistsException
     */
    public function testFailureMessagesAreLogged()
    {
        $command = new RegisterUserCommand();
        $exception = new UserAlreadyExistsException();

        $this->formatter->shouldReceive('commandReceived')->with($command)->once()->andReturn('foo bar');
        $this->formatter->shouldReceive('commandFailed')->with($command, $exception)->once()->andReturn('baz blat');

        $this->logger->shouldReceive('log')->with(LogLevel::DEBUG, 'foo bar')->once();
        $this->logger->shouldReceive('log')->with(LogLevel::ERROR, 'baz blat')->once();

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

    public function testNullMessagesAreNotLoggedForThatSpecificMessage()
    {
        $this->formatter->shouldReceive('commandReceived')->andReturnNull();
        $this->formatter->shouldReceive('commandHandled')->andReturn('foo bar');

        $this->logger->shouldReceive('log')->with(LogLevel::DEBUG, 'foo bar');

        $this->middleware->execute(new RegisterUserCommand(), $this->mockNext);
    }

    public function testCustomizableLogLevelsWork()
    {
        $middleware = new LoggerMiddleware($this->formatter, $this->logger, LogLevel::ALERT, LogLevel::CRITICAL);

        $this->formatter->shouldReceive('commandReceived')->andReturn('received');
        $this->formatter->shouldReceive('commandHandled')->andReturn('completed');

        $this->logger->shouldReceive('log')->with(LogLevel::ALERT, 'received');
        $this->logger->shouldReceive('log')->with(LogLevel::CRITICAL, 'completed');

        $middleware->execute(new RegisterUserCommand(), $this->mockNext);
    }

    /**
     * @expectedException \League\Tactician\Logger\Tests\Fixtures\UserAlreadyExistsException
     */
    public function testErrorLogLevelCanBeCustomized()
    {
        $middleware = new LoggerMiddleware(
            $this->formatter,
            $this->logger,
            LogLevel::DEBUG,
            LogLevel::DEBUG,
            LogLevel::CRITICAL
        );

        $this->formatter->shouldReceive('commandReceived')->andReturn('received');
        $this->formatter->shouldReceive('commandFailed')->andReturn('failed');

        $this->logger->shouldReceive('log')->with(LogLevel::DEBUG, 'received');
        $this->logger->shouldReceive('log')->with(LogLevel::CRITICAL, 'failed');

        $middleware->execute(
            new RegisterUserCommand(),
            function () {
                throw new UserAlreadyExistsException();
            }
        );
    }
}
