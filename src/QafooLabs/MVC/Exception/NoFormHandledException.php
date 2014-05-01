<?php

namespace QafooLabs\MVC\Exception;

use RuntimeException;

class NoFormHandledException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('You have to call $formRequest->handle() before calling other methods on the FormRequest.');
    }
}
