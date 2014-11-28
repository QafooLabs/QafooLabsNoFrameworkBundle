<?php

namespace QafooLabs\Bundle\NoFrameworkBundle\EventListener;

use QafooLabs\MVC\RedirectRoute;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;

/**
 * Detect GotoPage controller results and convert them to a redirect.
 */
class RedirectListener
{
    /**
     * @var \Symfony\Component\Routing\Generator\UrlGeneratorInterface
     */
    private $router;

    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $redirect = $event->getControllerResult();

        if ( ! ($redirect instanceof RedirectRoute)) {
            return;
        }

        $response = $redirect->getResponse();

        if ( ! $response) {
            $response = new Response("", 302);
        }

        $url = $this->router->generate(
            $redirect->getRouteName(),
            $redirect->getParameters()
        );
        $response->headers->set('Location', $url);

        $event->setResponse($response);
    }
}

