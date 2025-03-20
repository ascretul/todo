<?php

declare(strict_types=1);

namespace App\Exceptions;

use Throwable;

/**
 * Http Exception
 *
 * @copyright Elena Cretul
 */
class HttpException extends RuntimeException
{
    public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        \assert($code >= 400 && $code <= 600);
        parent::__construct($message, $code, $previous);
    }
}
