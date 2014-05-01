<?php

namespace QafooLabs\MVC\Exception;

use RuntimeException;

class NoFormBoundException extends RuntimeException
{
    public function __construct($name)
    {
        parent::__construct('You have to call $formRequest->handle() or $formRequest->bind() before calling other methods on the FormRequest.');
    }
}
