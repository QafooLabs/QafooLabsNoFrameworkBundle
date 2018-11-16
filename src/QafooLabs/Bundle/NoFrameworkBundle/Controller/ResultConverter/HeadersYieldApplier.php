<?php

namespace QafooLabs\Bundle\NoFrameworkBundle\Controller\ResultConverter;

use QafooLabs\MVC\Headers;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class HeadersYieldApplier implements ControllerYieldApplier
{
    public function supports($yield)
    {
        return $yield instanceof Headers;
    }

    public function apply($yield, Request $request, Response $response)
    {
        foreach ($yield->values as $key => $value) {
            $response->headers->set($key, $value);
        }
    }
}
