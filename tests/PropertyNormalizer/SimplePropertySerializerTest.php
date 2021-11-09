<?php
namespace League\Tactician\Logger\Tests\PropertyNormalizer;

use League\Tactician\Logger\PropertyNormalizer\SimplePropertyNormalizer;
use League\Tactician\Logger\Tests\Fixtures\RegisterUserCommand;
use PHPUnit\Framework\TestCase;

class SimplePropertyNormalizerTest extends TestCase
{
    /**
     * @var SimplePropertyNormalizer
     */
    private $normalizer;

    protected function setUp(): void
    {
        $this->normalizer = new SimplePropertyNormalizer();
    }

    public function testCommandPropertiesCanBeDumpedToString()
    {
        $this->assertEquals(
            ["name" => "Alice", "emailAddress" => "alice@example.org", "age" => 30.5, "createdAt" => "object(DateTime)",
            "file" => "resource(stream)", "empty" => null, "options" => "*array*"],
            $this->normalizer->normalize(new RegisterUserCommand())
        );
    }
}
