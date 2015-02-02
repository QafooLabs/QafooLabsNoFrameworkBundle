<?php

namespace QafooLabs\Bundle\NoFrameworkBundle\ParamConverter;

use Symfony\Component\DependencyInjection\ContainerInterface;

class SymfonyServiceProvider implements ServiceProviderInterface
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
     * {@inheritDoc}
     */
    public function getFormFactory()
    {
        return $this->container->get('form.factory');
    }

    /**
     * {@inheritDoc}
     */
    public function getSecurityContext()
    {
        return $this->container->get('security.context');
    }
}
