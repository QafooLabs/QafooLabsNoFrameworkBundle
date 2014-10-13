<?php

namespace QafooLabs\MVC;

/**
 * Flash Message Abstraction
 */
interface Flash
{
    /**
     * @param string $type
     * @param string $message
     */
    public function add($type, $message);
}
