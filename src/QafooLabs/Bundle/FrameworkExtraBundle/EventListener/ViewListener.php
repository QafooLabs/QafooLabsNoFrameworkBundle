<?php

namespace QafooLabs\Bundle\FrameworkExtraBundle\EventListener;

use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\Response;

use QafooLabs\Bundle\FrameworkExtraBundle\View\TemplateGuesser;

/**
 * Converts ViewStruct and arrays from Controllers into Response objects.
 *
 * Guess the template names with the same algorithm that @Template()
 * in Sensio's FrameworkExtraBundle uses. Works for both arrays
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
     * @var QafooLabs\Bundle\FrameworkExtraBundle\View\TemplateGuesser
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
        $viewParams = $event->getControllerResult();

        if (is_object($viewParams) && !($viewParams instanceof Response)) {
            $viewParams = array('view' => $viewParams);
        }

        if (is_array($viewParams)) {
            if (!isset($viewParams['view'])) {
                $viewParams['view'] = $viewParams;
            }

            $event->setResponse(
                $this->makeResponseFor($controller, $viewParams, $request->getRequestFormat())
            );
        }
    }

    private function makeResponseFor($controller, array $viewParams, $requestFormat)
    {
        $viewName = $this->guesser->guessControllerTemplateName($controller, $requestFormat, $this->engine);

        $response = new Response();
        $response->setContent($this->templating->render($viewName, $viewParams));

        return $response;
    }
}

