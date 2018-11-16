<?php

namespace QafooLabs\Bundle\NoFrameworkBundle\Controller\ResultConverter;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class CookieYieldApplier implements ControllerYieldApplier
{
    public function supports($yield)
    {
        return $yield instanceof Cookie;
    }

    public function apply($yield, Request $request, Response $response)
    {
        $response->headers->setCookie($yield);
    }
}
