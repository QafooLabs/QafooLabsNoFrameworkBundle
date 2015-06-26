<?php

namespace QafooLabs\Bundle\NoFrameworkBundle\ParamConverter;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

interface ServiceProviderInterface
{
    /**
     * @return FormFactoryInterface
     */
    public function getFormFactory();

    /**
     * @return TokenStorageInterface
     */
    public function getTokenStorage();

    /**
     * @return AuthorizationCheckerInterface
     */
    public function getAuthorizationChecker();
}
