<?php
namespace League\Tactician\Logger\Tests\PropertySerializer;

use League\Tactician\Logger\PropertySerializer\SimplePropertySerializer;
use League\Tactician\Logger\Tests\Fixtures\RegisterUserCommand;

class SimplePropertySerializerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SimplePropertySerializer
     */
    private $serializer;

    protected function setUp()
    {
        $this->serializer = new SimplePropertySerializer();
    }

    public function testCommandPropertiesCanBeDumpedToString()
    {
        $this->assertJsonStringEqualsJsonString(
            '{"name":"Alice","emailAddress":"alice@example.org","age":30.5,"createdAt":"object(DateTime)",' .
            '"file":"resource(stream)","empty":"*null*","options":"*array*"}',
            $this->serializer->encode(new RegisterUserCommand())
        );
    }
}
