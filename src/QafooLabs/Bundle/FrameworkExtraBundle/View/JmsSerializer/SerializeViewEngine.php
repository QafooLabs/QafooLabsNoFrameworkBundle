<?php

namespace QafooLabs\Bundle\FrameworkExtraBundle\View\JmsSerializer;

use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Templating\TemplateReference;
use JMS\Serializer\SerializerInterface;

/**
 * Template Engine that serializes parameters with JMS Serializer.
 */
class SerializeViewEngine implements EngineInterface
{
    private $serializer;

    public function __construct(SerializerInterface $serializer = null)
    {
        $this->serializer = $serializer;
    }

    public function render($name, array $parameters = array())
    {
        list ($name, $format, $engine) = explode(".", $name);

        return $this->serializer->serialize($parameters['view'], $format);
    }

    public function exists($name)
    {
        return $this->isSerializableFormat($name);
    }

    public function supports($name)
    {
        return $this->isSerializableFormat($name);
    }

    private function isSerializableFormat($name)
    {
        if ($name instanceof TemplateReference) {
            $format = $name->get('format');
        } else {
            list ($logicalName, $format, $engine) = explode(".", (string)$name);
        }

        return in_array($format, array('json', 'xml'));
    }
}

