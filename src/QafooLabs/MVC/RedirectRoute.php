<?php

namespace QafooLabs\MVC;

use Symfony\Component\HttpFoundation\Response;

class RedirectRoute
{
    private $routeName;
    private $parameters;
    private $response;

    public function __construct($routeName, array $parameters = array(), Response $response = null)
    {
        $this->routeName = $routeName;
        $this->parameters = $parameters;
        $this->response = $response;
    }

    public function getRouteName()
    {
        return $this->routeName;
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    public function getResponse()
    {
        return $this->response;
    }
}
