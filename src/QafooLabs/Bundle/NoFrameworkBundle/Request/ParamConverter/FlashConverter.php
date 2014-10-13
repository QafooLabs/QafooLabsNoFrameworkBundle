<?php

namespace QafooLabs\Bundle\NoFrameworkBundle\Request\ParamConverter;

use QafooLabs\Bundle\NoFrameworkBundle\SymfonyFlashBag;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

class FlashConverter implements ParamConverterInterface
{
    public function apply(Request $request, ParamConverter $configuration)
    {
        $request->attributes->set(
            $configuration->getName(),
            new SymfonyFlashBag($request->getSession()->getFlashBag())
        );
    }

    public function supports(ParamConverter $configuration)
    {
        if (null === $configuration->getClass()) {
            return false;
        }

        return "QafooLabs\\MVC\\Flash" === $configuration->getClass();
    }
}
