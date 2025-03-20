<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

/**
 * Runtime Exception.
 *
 * Our local implementation of RuntimeException.
 * It allows us to not simply extend the general Exception.
 * We use it as parent for exceptions that can happen at Runtime of the Application.
 * PHP RuntimeException is considered by IDE as unchecked exception - no need
 * to be declared in a method throws clause, same as Java RuntimeException.
 * That's why it was not possible to use it as parent for our Exceptions.
 *
 * @copyright Elena Cretul
 */
class RuntimeException extends Exception
{
}
