<?php

namespace QafooLabs\Bundle\NoFrameworkBundle\ParamConverter;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

interface ServiceProviderInterface
{
    /**
     * @return FormFactoryInterface
     */
    public function getFormFactory();

    /**
     * @return SecurityContextInterface
     */
    public function getSecurityContext();
}
