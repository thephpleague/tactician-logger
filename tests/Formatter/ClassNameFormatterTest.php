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
    protected $formatter;

    protected function setUp()
    {
        $this->formatter = new ClassNameFormatter();
    }

    public function testCommandSuccessCreatesExpectedMessage()
    {
        $this->assertEquals(
            'Command succeeded: ' . RegisterUserCommand::class,
            $this->formatter->commandHandled(new RegisterUserCommand())
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
        $this->assertEquals(
            'Command failed: ' . RegisterUserCommand::class,
            $this->formatter->commandFailed(new RegisterUserCommand())
        );
    }

    public function testCommandContextCreatesExpectedContext()
    {
        $this->assertEquals(
            ['class' => RegisterUserCommand::class],
            $this->formatter->commandContext(new RegisterUserCommand())
        );
    }

    public function testCompleteContextOnFailureWithExceptionInfo()
    {
        $exception = new UserAlreadyExistsException("foo bar baz");
        $this->assertEquals(
            [
                'error' => [
                    'class' => UserAlreadyExistsException::class,
                    'message' => 'foo bar baz',
                ],
                'current' => 'context'
            ],
            $this->formatter->failureContext(['current' => 'context'], $exception)
        );
    }
}
