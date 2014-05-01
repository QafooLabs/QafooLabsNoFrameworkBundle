<?php

namespace QafooLabs\MVC\Exception;

use RuntimeException;

class FormAlreadyHandledException extends RuntimeException
{
    public function __construct($name)
    {
        parent::__construct(sprintf(
            'The \Qafoo\MVC\FormRequest was already handled with form %s earlier. ' .
            'You can only use a FormRequest with exactly one form.',
            $name
        ));
    }
}
