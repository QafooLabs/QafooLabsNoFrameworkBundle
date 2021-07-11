<?php

namespace QafooLabs\Bundle\NoFrameworkBundle\View;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class ExtraBundleInteropTemplateGuesser
 *
 * @package QafooLabs\Bundle\NoFrameworkBundle\View
 */
class ExtraBundleInteropTemplateGuesser implements TemplateGuesser
{
    /** @var TemplateGuesser */
    private $templateGuesser;

    /** @var \Symfony\Component\HttpFoundation\RequestStack */
    private $requestStack;

    public function __construct(TemplateGuesser $templateGuesser, RequestStack $requestStack)
    {
        $this->templateGuesser = $templateGuesser;
        $this->requestStack    = $requestStack;
    }

    /** {@inheritdoc} */
    public function guessControllerTemplateName($controller, $actionName, $format, $engine)
    {
        return $this->detectExtraBundleTemplate() ?:
          $this->templateGuesser->guessControllerTemplateName(
            $controller,
            $actionName,
            $format,
            $engine
          );
    }

    /**
     * @return string|null
     */
    private function detectExtraBundleTemplate()
    {
        $template       = null;
        $currentRequest = $this->requestStack->getCurrentRequest();

        if ($currentRequest->attributes->has('_template')) {
            $templateAttr = $currentRequest->attributes->get('_template');
            if ($templateAttr instanceof Template) {
                $template = (string)$templateAttr->getTemplate();
            }
        }

        return $template;
    }
}

