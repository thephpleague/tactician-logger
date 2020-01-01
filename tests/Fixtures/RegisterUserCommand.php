<?php

declare(strict_types=1);

namespace League\Tactician\Logger\Tests\Fixtures;

use DateTime;
use function fclose;
use function fopen;

/**
 * A mock command.
 *
 * It has a ridiculous number of properties to help test different types of
 * property serialization.
 *
 * phpcs:disable
 */
class RegisterUserCommand
{
    /** @var string */
    public $name = 'Alice';

    /** @var string */
    protected $emailAddress = 'alice@example.org';

    /** @var float */
    private $age = 30.5;

    /** @var DateTime */
    private $createdAt;

    /** @var false|resource */
    private $file;

    /** @var null */
    private $empty = null;

    /** @var array<string,string> */
    private $options = ['foo' => 'thing 1', 'bar' => 'thing 2'];

    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->file      = fopen(__FILE__, 'r');
    }

    public function __destruct()
    {
        if ($this->file === false) {
            return;
        }

        fclose($this->file);
    }
}
