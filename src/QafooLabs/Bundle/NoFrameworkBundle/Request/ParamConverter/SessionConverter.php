<?php

namespace QafooLabs\Bundle\NoFrameworkBundle\Request\ParamConverter;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

class SessionConverter implements ParamConverterInterface
{
    public function apply(Request $request, ParamConverter $configuration)
    {
        $request->attributes->set(
            $configuration->getName(),
            $request->getSession()
        );
    }

    public function supports(ParamConverter $configuration)
    {
        if (null === $configuration->getClass()) {
            return false;
        }

        return is_subclass_of($configuration->getClass(), "Symfony\\Component\\HttpFoundation\\Session\\SessionInterface") ||
               $configuration->getClass() === "Symfony\\Component\\HttpFoundation\\Session\\SessionInterface";
    }
}
