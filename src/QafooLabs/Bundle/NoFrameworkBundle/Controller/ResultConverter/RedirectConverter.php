<?php

namespace QafooLabs\Bundle\NoFrameworkBundle\Controller\ResultConverter;

use QafooLabs\MVC\RedirectRoute;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class RedirectConverter implements ControllerResultConverter
{
    private $router;

    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    public function supports($result)
    {
        return ($redirect instanceof RedirectRoute);
    }

    public function convert($result, Request $request, Response $response = null) : Response
    {
        if ( ! $response) {
            $response = new Response("", 302);
        }

        $response->headers->set(
            'Location',
            $this->router->generate(
                $redirect->getRouteName(),
                $redirect->getParameters()
            )
        );

        return $response;
    }
}
