<?php

declare(strict_types=1);

namespace CastroItalo\EchoQuery\Exceptions;

use Exception;

/**
 * Represents exceptions specific to the query builder operations.
 *
 * The BuilderException class is designed to encapsulate errors that occur within the
 * query building process of the EchoQuery library. It extends the standard PHP Exception
 * class, providing enhanced context for troubleshooting and debugging. This exception
 * should be thrown when the query builder encounters a situation it cannot recover from,
 * such as invalid input parameters or configuration issues that prevent a query from being
 * correctly constructed or executed.
 *
 * @author castroitalo <dev.castro.italo@gmail.com>
 * @package CastroItalo\EchoQuery\Exceptions
 */
final class BuilderException extends Exception
{
    /**
     * Constructs a new BuilderException instance.
     *
     * This constructor initializes a new BuilderException with a specific error message
     * and error code, along with an optional previous exception for exception chaining.
     * Exception chaining is useful for debugging, as it allows developers to trace back
     * through the sequence of exceptions that led to the current error.
     *
     * @param string $message The error message that explains the reason for the exception.
     *                        This message should be clear and concise, providing enough
     *                        information for developers to identify the source of the error.
     * @param int $code A unique error code associated with this exception type. Error codes
     *                  facilitate quick identification of the error scenario and can be used
     *                  for more granular exception handling and logging.
     * @param Exception|null $previous The previous exception in the chain, if any. Providing
     *                                 the previous exception is optional but recommended where
     *                                 relevant, to preserve error history and context.
     */
    public function __construct(string $message, int $code, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Provides a string representation of the exception.
     *
     * Overrides the default getQuery method to provide a custom string representation of
     * the exception. This representation includes the class name, error code, and error message,
     * making it suitable for logging or displaying in a debugging context. The format is
     * designed to be human-readable and informative, facilitating quicker troubleshooting.
     *
     * @return string A string containing the class name, error code, and error message of the
     *                exception. This string format aids in distinguishing this exception from
     *                others in logs or error outputs.
     */
    public function getQuery(): string
    {
        return __CLASS__ . '[' . $this->code . ']: ' . $this->message;
    }
}
