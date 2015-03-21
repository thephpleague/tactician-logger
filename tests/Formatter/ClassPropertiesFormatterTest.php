<?php
namespace League\Tactician\Logger\Tests\Formatter;

use League\Tactician\Logger\Formatter\ClassPropertiesFormatter;
use League\Tactician\Logger\PropertySerializer\PropertySerializer;
use League\Tactician\Logger\Tests\Fixtures\RegisterUserCommand;
use League\Tactician\Logger\Tests\Fixtures\UserAlreadyExistsException;
use Mockery;
use Mockery\MockInterface;

class ClassPropertiesFormatterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ClassPropertiesFormatter
     */
    private $formatter;

    /**
     * @var PropertySerializer|MockInterface
     */
    private $serializer;

    protected function setUp()
    {
        $this->serializer = Mockery::mock(PropertySerializer::class);
        $this->serializer->shouldReceive('encode')->andReturn('!!!');

        $this->formatter = new ClassPropertiesFormatter($this->serializer);
    }

    public function testCommandReceivedReturnsExpectedMessage()
    {
        $command = new RegisterUserCommand();

        $this->assertEquals(
            'Command received: League\Tactician\Logger\Tests\Fixtures\RegisterUserCommand !!!',
            $this->formatter->commandReceived($command)
        );
    }

    public function testCommandCompletedReturnsExpectedMessage()
    {
        $command = new RegisterUserCommand();

        $this->assertEquals(
            'Command completed: League\Tactician\Logger\Tests\Fixtures\RegisterUserCommand !!!',
            $this->formatter->commandCompleted($command)
        );
    }

    public function testCommandFailedReturnsExpectedMessage()
    {
        $command = new RegisterUserCommand();
        $exception = new UserAlreadyExistsException('foo bar');

        $this->assertEquals(
            'Command failed: League\Tactician\Logger\Tests\Fixtures\RegisterUserCommand !!! ' .
            'threw the exception ' . UserAlreadyExistsException::class. ' (foo bar)',
            $this->formatter->commandFailed($command, $exception)
        );
    }
}
