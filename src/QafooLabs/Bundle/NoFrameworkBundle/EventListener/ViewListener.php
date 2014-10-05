<?php

namespace QafooLabs\Bundle\NoFrameworkBundle\EventListener;

use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpFoundation\Response;

use QafooLabs\Bundle\NoFrameworkBundle\View\TemplateGuesser;
use QafooLabs\MVC\TemplateView;

/**
 * Converts ViewStruct and arrays from Controllers into Response objects.
 *
 * Guess the template names with the same algorithm that @Template()
 * in Sensio's NoFrameworkBundle uses. Works for both arrays
 * but also objects. In the later case the template is passed
 * a single variable 'view' containing the object. For arrays
 * a new key 'view' is created as well with a copy of the data
 * to allow forwards compatible migration towards the view models.
 *
 * That means you should always access variables through ``view.var``
 */
class ViewListener
{
    /**
     * @var Symfony\Component\Templating\EngineInterface
     */
    private $templating;

    /**
     * @var QafooLabs\Bundle\NoFrameworkBundle\View\TemplateGuesser
     */
    private $guesser;

    /**
     * @var string
     */
    private $engine;

    /**
     * @param string $engine
     */
    public function __construct(EngineInterface $templating, TemplateGuesser $guesser, $engine)
    {
        $this->templating = $templating;
        $this->guesser    = $guesser;
        $this->engine     = $engine;
    }

    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $request = $event->getRequest();

        if ( ! $request->attributes->has('_controller')) {
            return;
        }

        $controller = $request->attributes->get('_controller');
        $templateView = $event->getControllerResult();

        if (!$controller || $templateView instanceof Response) {
            return;
        }

        if ( ! ($templateView instanceof TemplateView)) {
            $templateView = new TemplateView($templateView);
        }

        $event->setResponse(
            $this->makeResponseFor($controller, $templateView, $request->getRequestFormat())
        );
    }

    private function makeResponseFor($controller, TemplateView $templateView, $requestFormat)
    {
        $viewName = $this->guesser->guessControllerTemplateName(
            $controller,
            $templateView->getActionTemplateName(),
            $requestFormat,
            $this->engine
        );

        return new Response(
            $this->templating->render($viewName, $templateView->getViewParams()),
            $templateView->getStatusCode(),
            $templateView->getHeaders()
        );
    }
}

