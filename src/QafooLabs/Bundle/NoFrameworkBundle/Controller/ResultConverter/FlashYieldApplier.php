<?php

namespace QafooLabs\Bundle\NoFrameworkBundle\Controller\ResultConverter;

use QafooLabs\MVC\Flash;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class FlashYieldApplier implements ControllerYieldApplier
{
    public function supports($yield)
    {
        return $yield instanceof Flash;
    }

    public function apply($yield, Request $request, Response $response)
    {
        if (!$request->hasSession()) {
            return;
        }

        $request->getSession()->getFlashBag()->add($yield->type, $yield->message);
    }
}
