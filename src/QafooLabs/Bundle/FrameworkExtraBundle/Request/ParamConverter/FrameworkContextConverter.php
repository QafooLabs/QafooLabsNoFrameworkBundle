<?php

namespace QafooLabs\Bundle\FrameworkExtraBundle\Request\ParamConverter;

use QafooLabs\Bundle\FrameworkExtraBundle\SymfonyFrameworkContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Convert FrameworkContext typehint in controllers.
 */
class FrameworkContextConverter implements ParamConverterInterface
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Stores the object in the request.
     *
     * @param Request        $request       The request
     * @param ParamConverter $configuration Contains the name, class and options of the object
     *
     * @return boolean True if the object has been successfully set, else false
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $request->attributes->set(
            $configuration->getName(),
            new SymfonyFrameworkContext(
                $this->container->get('security.context'),
                $this->container->getParameter('kernel.environment'),
                $this->container->getParameter('kernel.debug')
            )
        );
    }

    /**
     * Checks if the object is supported.
     *
     * @param ParamConverter $configuration Should be an instance of ParamConverter
     *
     * @return boolean True if the object is supported, else false
     */
    public function supports(ParamConverter $configuration)
    {
        if (null === $configuration->getClass()) {
            return false;
        }

        return "QafooLabs\\MVC\\FrameworkContext" === $configuration->getClass();
    }
}
