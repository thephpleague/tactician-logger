<?php
namespace League\Tactician\Logger\Tests\Formatter;

use League\Tactician\Logger\Formatter\ClassNameFormatter;
use League\Tactician\Logger\Tests\Fixtures\RegisterUserCommand;
use League\Tactician\Logger\Tests\Fixtures\UserAlreadyExistsException;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Mockery;
use Psr\Log\LogLevel;

class ClassNameFormatterTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var ClassNameFormatter
     */
    protected $formatter;

    /**
     * @var LoggerInterface|Mockery\MockInterface
     */
    protected $logger;

    protected function setUp(): void
    {
        $this->formatter = new ClassNameFormatter();
        $this->logger = Mockery::mock(LoggerInterface::class);
    }

    public function tearDown(): void
    {
        Mockery::close();
    }

    public function testBasicSuccessMessageIsLogged()
    {
        $this->logger->shouldReceive('log')->with(
            LogLevel::DEBUG,
            'Command succeeded: ' . RegisterUserCommand::class,
            []
        );

        $this->formatter->logCommandSucceeded($this->logger, new RegisterUserCommand(), null);
    }

    public function testCommandReceivedCreatesExpectedMessage()
    {
        $this->logger->shouldReceive('log')->with(
            LogLevel::DEBUG,
            'Command received: ' . RegisterUserCommand::class,
            []
        );

        $this->formatter->logCommandReceived($this->logger, new RegisterUserCommand());
    }

    public function testCommandFailedCreatesExpectedMessage()
    {
        $exception = new UserAlreadyExistsException();

        $this->logger->shouldReceive('log')->with(
            LogLevel::ERROR,
            'Command failed: ' . RegisterUserCommand::class,
            ['exception' => $exception]
        );

        $this->formatter->logCommandFailed($this->logger, new RegisterUserCommand(), $exception);
    }

    public function testCustomLogLevels()
    {
        $formatter = new ClassNameFormatter(LogLevel::WARNING, LogLevel::NOTICE, LogLevel::EMERGENCY);

        $this->logger->shouldReceive('log')->with(LogLevel::WARNING, Mockery::any(), Mockery::any())->once();
        $formatter->logCommandReceived($this->logger, new RegisterUserCommand());

        $this->logger->shouldReceive('log')->with(LogLevel::NOTICE, Mockery::any(), Mockery::any())->once();
        $formatter->logCommandSucceeded($this->logger, new RegisterUserCommand(), null);

        $this->logger->shouldReceive('log')->with(LogLevel::EMERGENCY, Mockery::any(), Mockery::any())->once();
        $formatter->logCommandFailed($this->logger, new RegisterUserCommand(), new UserAlreadyExistsException());
    }
}
