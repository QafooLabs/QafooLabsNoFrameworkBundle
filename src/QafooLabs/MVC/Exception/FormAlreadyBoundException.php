<?php

namespace QafooLabs\MVC\Exception;

use RuntimeException;

class FormAlreadyBoundException extends RuntimeException
{
    public function __construct($name)
    {
        parent::__construct(sprintf(
            'The \Qafoo\MVC\FormRequest was already bound to the form %s earlier. ' .
            'You can only use a FormRequest with exactly one form.',
            $name
        ));
    }
}
