<?php
namespace League\Tactician\Logger\Formatter;

use League\Tactician\Logger\PropertySerializer\PropertySerializer;
use League\Tactician\Logger\PropertySerializer\SimplePropertySerializer;
use Exception;

/**
 * Formatter that includes the Command's name and properties for more detail
 */
class ClassPropertiesFormatter implements Formatter
{
    /**
     * @var PropertySerializer
     */
    private $serializer;

    /**
     * @param PropertySerializer $serializer
     */
    public function __construct(PropertySerializer $serializer = null)
    {
        $this->serializer = $serializer ?: new SimplePropertySerializer();
    }

    /**
     * @param object $command
     * @return string|null
     */
    public function commandReceived($command)
    {
        return 'Command received: ' . get_class($command) . ' ' . $this->serializer->encode($command);
    }

    /**
     * @param object $command
     * @return string|null
     */
    public function commandHandled($command)
    {
        return 'Command succeeded: ' . get_class($command) . ' ' . $this->serializer->encode($command);
    }

    /**
     * @param object $command
     * @param Exception $exception
     * @return string|null
     */
    public function commandFailed($command, Exception $exception)
    {
        $exceptionClass = get_class($exception);
        $exceptionMessage = $exception->getMessage();

        return 'Command failed: ' . get_class($command) . ' ' . $this->serializer->encode($command)
            . " threw the exception {$exceptionClass} ({$exceptionMessage})";
    }
}
