<?php

namespace QafooLabs\Bundle\NoFrameworkBundle\EventListener;

use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use QafooLabs\Bundle\NoFrameworkBundle\Controller\ResultConverter\ControllerResultConverter;
use QafooLabs\Bundle\NoFrameworkBundle\View\TemplateGuesser;
use QafooLabs\MVC\TemplateView;
use Generator;

/**
 * Converts non Response results into various side effects from a controller.
 */
class ViewListener
{
    private $converters = [];

    public function addConverter(ControllerResultConverter $converter)
    {
        $this->converters[] = $converter;
    }

    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $request = $event->getRequest();

        if ( ! $request->attributes->has('_controller')) {
            return;
        }

        $controller = $request->attributes->get('_controller');
        $result = $event->getControllerResult();

        if (!$controller || $result instanceof Response) {
            return;
        }

        $response = ($result instanceof Generator)
            ? $this->unrollGenerator($result, $request)
            : $this->convert($result, $request, null);

        if ($response) {
            $event->setResponse($response);
        }
    }

    private function unrollGenerator(Generator $generator, Request $request): ?Response
    {
        $results = [];

        foreach ($generator as $element) {
            $results[] = $element;
        }

        if ($result = $generator->getReturn()) {
            $results[] = $result;
        }

        $response = null;
        foreach (array_reverse($results) as $result) {
            $response = $this->convert($result, $request, $response);
        }

        return $response;
    }

    private function convert($result, Request $request, Response $response = null): ?Response
    {
        foreach ($this->converters as $converter) {
            if ($converter->supports($result)) {
                return $converter->convert($result, $request, $response);
            }
        }

        return null;
    }
}

