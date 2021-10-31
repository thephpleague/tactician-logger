<?php

declare(strict_types=1);

namespace League\Tactician\Logger\PropertyNormalizer;

/**
 * Normalize an object into an array of scalars so they can added to a log context
 */
interface PropertyNormalizer
{
    /** @return array<string,mixed> */
    public function normalize(object $value): array;
}
