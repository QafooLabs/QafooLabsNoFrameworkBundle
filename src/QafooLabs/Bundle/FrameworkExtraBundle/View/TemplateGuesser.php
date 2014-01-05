<?php

namespace QafooLabs\Bundle\FrameworkExtraBundle\View;

interface TemplateGuesser
{
    /**
     * Return a template reference for the given controller, format, engine
     *
     * @param string $controller
     * @param string $format
     * @param string $engine
     *
     * @return string
     */
    public function guessControllerTemplateName($controller, $format, $engine);
}
