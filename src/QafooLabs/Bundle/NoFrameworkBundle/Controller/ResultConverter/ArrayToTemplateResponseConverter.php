<?php

namespace QafooLabs\Bundle\NoFrameworkBundle\Controller\ResultConverter;

use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use QafooLabs\Bundle\NoFrameworkBundle\View\TemplateGuesser;
use QafooLabs\MVC\TemplateView;

/**
 * Convert array or {@link TemplateView} struct into templated response.
 *
 * Guess the template names with the same algorithm that @Template()
 * in Sensio's FrameworkExtraBundle uses.
*/
class ArrayToTemplateResponseConverter implements ControllerResultConverter
{
    private $templating;
    private $guesser;
    private $engine;

    public function __construct(EngineInterface $templating, TemplateGuesser $guesser, string $engine)
    {
        $this->templating = $templating;
        $this->guesser = $guesser;
        $this->engine = $engine;
    }

    public function supports($result)
    {
        return is_array($result) || $result instanceof TemplateView;
    }

    public function convert($result, Request $request) : Response
    {
        $controller = $request->attributes->get('_controller');

        if ( ! ($result instanceof TemplateView)) {
            $result = new TemplateView($result);
        }

        return $this->makeResponseFor(
            $controller,
            $result,
            $request->getRequestFormat()
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
