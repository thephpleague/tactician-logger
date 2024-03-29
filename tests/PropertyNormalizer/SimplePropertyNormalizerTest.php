<?php

declare(strict_types=1);

namespace League\Tactician\Logger\Tests\PropertyNormalizer;

use League\Tactician\Logger\PropertyNormalizer\SimplePropertyNormalizer;
use League\Tactician\Logger\Tests\Fixtures\RegisterUserCommand;
use PHPUnit\Framework\TestCase;

class SimplePropertyNormalizerTest extends TestCase
{
    private SimplePropertyNormalizer $normalizer;

    protected function setUp(): void
    {
        $this->normalizer = new SimplePropertyNormalizer();
    }

    public function testCommandPropertiesCanBeDumpedToString(): void
    {
        $this->assertEquals(
            [
                'name' => 'Alice',
                'emailAddress' => 'alice@example.org',
                'age' => 30.5,
                'createdAt' => 'object(DateTime)',
                'file' => 'resource(stream)',
                'empty' => null,
                'options' => '*array*',
            ],
            $this->normalizer->normalize(new RegisterUserCommand())
        );
    }
}
