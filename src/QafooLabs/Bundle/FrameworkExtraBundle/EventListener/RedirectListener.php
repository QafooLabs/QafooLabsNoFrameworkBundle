<?php

namespace QafooLabs\Bundle\FrameworkExtraBundle\EventListener;

use QafooLabs\MVC\RedirectRouteResponse;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
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

        if ( ! ($redirect instanceof RedirectRouteResponse)) {
            return;
        }

        $event->setResponse(
            new RedirectResponse(
                $this->router->generate(
                    $redirect->getRouteName(),
                    $redirect->getParameters()
                ),
                $redirect->getStatusCode(),
                $redirect->getHeaders()
            )
        );
    }
}

