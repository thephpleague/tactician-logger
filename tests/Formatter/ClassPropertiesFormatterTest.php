<?php
namespace League\Tactician\Logger\Tests\Formatter;

use League\Tactician\Logger\Formatter\ClassPropertiesFormatter;
use League\Tactician\Logger\PropertyNormalizer\PropertyNormalizer;
use League\Tactician\Logger\Tests\Fixtures\RegisterUserCommand;
use League\Tactician\Logger\Tests\Fixtures\UserAlreadyExistsException;
use Mockery;
use Mockery\MockInterface;

class ClassPropertiesFormatterTest extends ClassNameFormatterTest
{
    /**
     * @var PropertyNormalizer|MockInterface
     */
    private $normalizer;

    protected function setUp()
    {
        $this->normalizer = Mockery::mock(PropertyNormalizer::class);
        $this->normalizer->shouldReceive('normalize')->andReturn(['!!!']);

        $this->formatter = new ClassPropertiesFormatter($this->normalizer);
    }

    public function testCommandContextReturnsNormalizationResult()
    {
        $this->assertEquals(
            ['!!!'],
            $this->formatter->commandContext(new RegisterUserCommand())
        );
    }
}
