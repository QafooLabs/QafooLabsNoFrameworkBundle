<?php

namespace QafooLabs\MVC;

class RedirectRouteResponse
{
    private $routeName;
    private $parameters;
    private $statusCode;
    private $headers;

    public function __construct($routeName, array $parameters = array(), $statusCode = 301, array $headers = array())
    {
        $this->routeName = $routeName;
        $this->parameters = $parameters;
        $this->statusCode = $statusCode;
        $this->headers = $headers;
    }

    public function getRouteName()
    {
        return $this->routeName;
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function getHeaders()
    {
        return $this->headers;
    }
}
