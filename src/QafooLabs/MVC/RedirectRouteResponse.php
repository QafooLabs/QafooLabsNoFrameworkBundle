<?php

namespace QafooLabs\MVC;

use Symfony\Component\HttpFoundation\Response;

class RedirectRouteResponse extends RedirectRoute
{
    public function __construct($routeName, array $parameters = array(), $statusCode = 301, array $headers = array())
    {
        parent::__construct($routeName, $parameters, new Response("", $statusCode, $headers));
    }

    public function getStatusCode()
    {
        return $this->getResponse()->getStatusCode();
    }

    public function getHeaders()
    {
        return $this->getResponse()->headers->all();
    }
}
