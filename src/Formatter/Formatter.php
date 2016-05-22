<?php
namespace League\Tactician\Logger\Formatter;

use Exception;

/**
 * Converts incoming Commands into log messages.
 *
 * Each method is invoked for a specific use case and can return either string
 * or null. Any string will be written to the logger, nulls will be ignored and
 * write nothing to the log.
 *
 * commandReceived() and commandHandled() receive only the command, however
 * commandFailed() also receives the exception that caused the failure.
 *
 * commandContext() and failureContext() create a key/value array that can be
 * stored in your log's context. commandContext() is *only* added when a
 * a commmand is handled and when a command is received, NOT when it fails.
 *
 * commandFailed(), on the other hand, is extra data you can choose to include
 * when a command fails. It has access to the previous context, as well as the
 * original exception. The output of commandFailed() is what's directly passed
 * to the log, so if you'd like it to contain the same data as commandContext()
 * you should merge the two before returning from commandFailed.
 *
 * If you'd prefer to have the messages blank or the contexts empty for a
 * particular step, just return null or an empty array respectively.
 *
 * For an example of what this all looks like, take a look at the
 * ClassNameFormatter example bundled with this package.
 *
 * For more information about log contexts:
 * @see https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md#13-context
 */
interface Formatter
{
    /**
     * @param object $command
     * @return string|null
     */
    public function commandReceived($command);

    /**
     * @param object $command
     * @return string|null
     */
    public function commandHandled($command);

    /**
     * @param object $command
     * @return string|null
     */
    public function commandFailed($command);

    /**
     * @param object $command
     * @return array
     */
    public function commandContext($command);

    /**
     * @param array $currentContext
     * @param Exception $exception
     * @return array
     */
    public function failureContext(array $currentContext, \Exception $exception);
}
