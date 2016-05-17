<?php
namespace League\Tactician\Logger\Formatter;

use League\Tactician\Logger\PropertyNormalizer\PropertyNormalizer;
use League\Tactician\Logger\PropertyNormalizer\SimplePropertyNormalizer;
use Exception;

/**
 * Formatter that includes the Command's name and properties for more detail
 */
class ClassPropertiesFormatter extends ClassNameFormatter
{
    /**
     * @var PropertyNormalizer
     */
    private $normalizer;

    /**
     * @param PropertyNormalizer $normalizer
     */
    public function __construct(PropertyNormalizer $normalizer = null)
    {
        $this->normalizer = $normalizer ?: new SimplePropertyNormalizer();
    }

    /**
     * {@inheritDoc}
     */
    public function commandContext($command)
    {
        return $this->normalizer->normalize($command);
    }
}
