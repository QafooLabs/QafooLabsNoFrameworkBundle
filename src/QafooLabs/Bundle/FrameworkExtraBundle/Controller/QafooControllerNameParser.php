<?php

namespace QafooLabs\Bundle\FrameworkExtraBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\ControllerNameParser;
use Symfony\Component\DependencyInjection\ContainerInterface;

class QafooControllerNameParser
{
    private $symfonyParser;
    private $container;

    public function __construct(ControllerNameParser $parser, ContainerInterface $container)
    {
        $this->symfonyParser = $symfonyParser;
        $this->container = $container;
    }

    public function parse($controller)
    {
        $parts = explode(":", $controller);

        if (count($parts) === 3) {
            return $this->symfonyParser->parse($controller);
        }

        if (count($parts) !== 2) {
            throw new \RuntimeException("Cannot parse controller name");
        }

        return $this->parseServiceController($parts[0], $parts[1]);
    }

    private function parseServiceController($serviceId, $method)
    {
        $service = $this->container->get($serviceId);

        return \Doctrine\Common\Util\ClassUtils::getClass($service) . '::' . $method;
    }
}
