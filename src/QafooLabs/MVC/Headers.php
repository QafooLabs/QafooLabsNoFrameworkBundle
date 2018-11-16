<?php

namespace QafooLabs\MVC;

class Headers
{
    public $values = [];

    public function __construct(array $values)
    {
        $this->values = $values;
    }
}
