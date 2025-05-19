<?php

declare(strict_types=1);

namespace CommissionCalculator\Exception;

use Exception;
use Throwable;

/**
 * Exception thrown when input data is invalid
 */
class InvalidInputException extends Exception
{
    /**
     * Create a new InvalidInputException instance
     *
     * @param string $message The error message
     * @param int $code The error code
     * @param Throwable|null $previous The previous exception used for the exception chaining
     */
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
