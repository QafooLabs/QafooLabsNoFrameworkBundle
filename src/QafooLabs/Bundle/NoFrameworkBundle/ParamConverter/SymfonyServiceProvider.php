<?php

namespace QafooLabs\Bundle\NoFrameworkBundle\ParamConverter;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

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
    public function getTokenStorage()
    {
        return $this->container->get('security.token_storage');
    }

    /**
     * {@inheritDoc}
     */
    public function getAuthorizationChecker()
    {
        return $this->container->get('security.authorization_checker');
    }
}
