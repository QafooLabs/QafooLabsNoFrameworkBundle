<?php

namespace QafooLabs\Bundle\NoFrameworkBundle;

use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use QafooLabs\MVC\Flash;

class SymfonyFlashBag implements Flash
{
    private $flashBag;

    public function __construct(FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
    }

    public function add($type, $message)
    {
        $this->flashBag->add($type, $message);
    }
}
