<?php
namespace League\Tactician\Logger\Tests\Formatter;

use League\Tactician\Logger\Formatter\ClassNameFormatter;
use League\Tactician\Logger\Tests\Fixtures\RegisterUserCommand;
use League\Tactician\Logger\Tests\Fixtures\UserAlreadyExistsException;

class ClassNameFormatterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ClassNameFormatter
     */
    private $formatter;

    protected function setUp()
    {
        $this->formatter = new ClassNameFormatter();
    }

    public function testCommandSuccessCreatesExpectedMessage()
    {
        $this->assertEquals(
            'Command succeeded: ' . RegisterUserCommand::class,
            $this->formatter->commandCompleted(new RegisterUserCommand())
        );
    }

    public function testCommandReceivedCreatesExpectedMessage()
    {
        $this->assertEquals(
            'Command received: ' . RegisterUserCommand::class,
            $this->formatter->commandReceived(new RegisterUserCommand())
        );
    }

    public function testCommandFailedCreatesExpectedMessage()
    {
        $exception = new UserAlreadyExistsException("foo bar baz");

        $expectedMessage = 'Command failed: ' . RegisterUserCommand::class . ' threw the exception '
            . UserAlreadyExistsException::class . ' (foo bar baz)';

        $this->assertEquals(
            $expectedMessage,
            $this->formatter->commandFailed(new RegisterUserCommand(), $exception)
        );
    }
}
