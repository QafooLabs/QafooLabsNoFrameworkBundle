<?php

namespace QafooLabs\MVC\Exception;

use Exception;

/**
 * Thrown when accessing information about a user when none is authenticated.
 */
class UnauthenticatedUserException extends Exception
{
}
