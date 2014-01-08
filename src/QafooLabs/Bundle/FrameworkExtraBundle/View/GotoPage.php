<?php

namespace QafooLabs\Bundle\FrameworkExtraBundle\View;

class GotoPage
{
    private $routeName;
    private $parameters;

    public function __construct($routeName, array $parameters = array())
    {
        $this->routeName = $routeName;
        $this->parameters = $parameters;
    }

    public function getRouteName()
    {
        return $this->routeName;
    }

    public function getParameters()
    {
        return $this->parameters;
    }
}
