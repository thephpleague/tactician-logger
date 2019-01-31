<?php
namespace League\Tactician\Logger\Tests\Formatter;

use League\Tactician\Handler\CommandNameExtractor\CommandNameExtractor;
use League\Tactician\Logger\Formatter\ClassNameFormatter;
use League\Tactician\Logger\Formatter\CommandNameFormatter;
use League\Tactician\Logger\Tests\Fixtures\RegisterUserCommand;
use League\Tactician\Logger\Tests\Fixtures\UserAlreadyExistsException;
use Psr\Log\LoggerInterface;
use Mockery;
use Psr\Log\LogLevel;

class CommandNameFormatterTest extends \PHPUnit_Framework_TestCase
{
    /** @var string */
    private $mockedClassName;
    
    /**
     * @var CommandNameExtractor|MockInterface
     */
    private $commandNameExtractor;
    
    /**
     * @var ClassNameFormatter
     */
    protected $formatter;

    /**
     * @var LoggerInterface|Mockery\MockInterface
     */
    protected $logger;

    protected function setUp()
    {
        $this->mockedClassName = str_replace('\\', '_', RegisterUserCommand::class);
        
        $this->commandNameExtractor = Mockery::mock(CommandNameExtractor::class);
        $this->commandNameExtractor->shouldReceive('extract')->andReturn($this->mockedClassName);cd 
        
        $this->formatter = new CommandNameFormatter($this->commandNameExtractor);
        $this->logger = Mockery::mock(LoggerInterface::class);
    }

    public function testBasicSuccessMessageIsLogged()
    {
        $this->logger->shouldReceive('log')->with(
            LogLevel::DEBUG,
            'Command succeeded: ' . $this->mockedClassName,
            []
        );

        $this->formatter->logCommandSucceeded($this->logger, new RegisterUserCommand(), null);
    }

    public function testCommandReceivedCreatesExpectedMessage()
    {
        $this->logger->shouldReceive('log')->with(
            LogLevel::DEBUG,
            'Command received: ' . $this->mockedClassName,
            []
        );

        $this->formatter->logCommandReceived($this->logger, new RegisterUserCommand());
    }

    public function testCommandFailedCreatesExpectedMessage()
    {
        $exception = new UserAlreadyExistsException();

        $this->logger->shouldReceive('log')->with(
            LogLevel::ERROR,
            'Command failed: ' . $this->mockedClassName,
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
