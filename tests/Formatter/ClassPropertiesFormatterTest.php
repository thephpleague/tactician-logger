<?php
namespace League\Tactician\Logger\Tests\Formatter;

use League\Tactician\Logger\Formatter\ClassPropertiesFormatter;
use League\Tactician\Logger\PropertyNormalizer\PropertyNormalizer;
use League\Tactician\Logger\Tests\Fixtures\RegisterUserCommand;
use League\Tactician\Logger\Tests\Fixtures\UserAlreadyExistsException;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class ClassPropertiesFormatterTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var PropertyNormalizer|MockInterface
     */
    private $normalizer;

    /**
     * @var ClassPropertiesFormatter
     */
    protected $formatter;

    /**
     * @var LoggerInterface|Mockery\MockInterface
     */
    protected $logger;


    protected function setUp(): void
    {
        $this->normalizer = Mockery::mock(PropertyNormalizer::class);
        $this->normalizer->shouldReceive('normalize')->andReturn(['test' => 'data']);

        $this->logger = Mockery::mock(LoggerInterface::class);

        $this->formatter = new ClassPropertiesFormatter($this->normalizer);
    }

    public function testBasicSuccessMessageIsLogged()
    {
        $this->logger->shouldReceive('log')->with(
            LogLevel::DEBUG,
            'Command succeeded: ' . RegisterUserCommand::class,
            ['command' => ['test' => 'data']]
        );

        $this->formatter->logCommandSucceeded($this->logger, new RegisterUserCommand(), null);
    }

    public function testCommandReceivedCreatesExpectedMessage()
    {
        $this->logger->shouldReceive('log')->with(
            LogLevel::DEBUG,
            'Command received: ' . RegisterUserCommand::class,
            ['command' => ['test' => 'data']]
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
        $formatter = new ClassPropertiesFormatter(
            $this->normalizer,
            LogLevel::WARNING,
            LogLevel::NOTICE,
            LogLevel::EMERGENCY
        );

        $this->logger->shouldReceive('log')->with(LogLevel::WARNING, Mockery::any(), Mockery::any())->once();
        $formatter->logCommandReceived($this->logger, new RegisterUserCommand());

        $this->logger->shouldReceive('log')->with(LogLevel::NOTICE, Mockery::any(), Mockery::any())->once();
        $formatter->logCommandSucceeded($this->logger, new RegisterUserCommand(), null);

        $this->logger->shouldReceive('log')->with(LogLevel::EMERGENCY, Mockery::any(), Mockery::any())->once();
        $formatter->logCommandFailed($this->logger, new RegisterUserCommand(), new UserAlreadyExistsException());
    }
}
