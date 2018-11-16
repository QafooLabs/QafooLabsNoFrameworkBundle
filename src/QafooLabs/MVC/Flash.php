<?php

namespace QafooLabs\MVC;

/**
 * Flash Message Abstraction
 */
class Flash
{
    public $type;
    public $message;

    public function __construct(string $type, string $message)
    {
        $this->type = $type;
        $this->message = $message;
    }
}
