<?php

namespace QafooLabs\Bundle\FrameworkExtraBundle\View;

/**
 * Wraps a view that is rendering a template with all its data.
 */
class TemplateView
{
    private $viewParams;
    private $actionTemplateName;
    private $statusCode;
    private $headers;

    public function __construct($viewParams, $actionTemplateName = null, $statusCode = 200, array $headers = array())
    {
        $this->viewParams = $viewParams;
        $this->actionTemplateName = $actionTemplateName;
        $this->statusCode = $statusCode;
        $this->headers = $headers;
    }

    /**
     * @return array
     */
    public function getViewParams()
    {
        $viewParams = $this->viewParams;

        if (is_object($viewParams)) {
            $viewParams = array('view' => $viewParams);
        }

        if (!isset($viewParams['view'])) {
            $viewParams['view'] = $viewParams;
        }

        return $viewParams;
    }

    /**
     * @return string
     */
    public function getActionTemplateName()
    {
        return $this->actionTemplateName;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }
}
