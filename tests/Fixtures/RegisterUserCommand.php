<?php
namespace League\Tactician\Logger\Tests\Fixtures;

use DateTime;

/**
 * A mock command.
 *
 * It has a ridiculous number of properties to help test different types of
 * property serialization.
 */
class RegisterUserCommand
{
    public $name;

    protected $emailAddress;

    private $age;

    private $createdAt;

    private $file;

    private $empty;

    private $options;

    public function __construct()
    {
        $this->name = 'Alice';
        $this->emailAddress = 'alice@example.org';
        $this->age = 30.5;
        $this->createdAt = new DateTime();
        $this->file = fopen(__FILE__, 'r');
        $this->empty = null;
        $this->options = ['foo' => 'thing 1', 'bar' => 'thing 2'];
    }

    public function __destruct()
    {
        fclose($this->file);
    }
}
