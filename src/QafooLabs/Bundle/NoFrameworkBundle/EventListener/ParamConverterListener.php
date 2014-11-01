<?php

namespace QafooLabs\Bundle\NoFrameworkBundle\EventListener;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use QafooLabs\Bundle\NoFrameworkBundle\SymfonyFlashBag;
use QafooLabs\Bundle\NoFrameworkBundle\Request\SymfonyFormRequest;
use QafooLabs\Bundle\NoFrameworkBundle\SymfonyTokenContext;

/**
 * Convert the request parameters into objects when typehinted.
 *
 * This replicates the SensioFrameworkExtraBundle behavior but keeps it simple
 * and without a dependency to allow usage outside Symfony Framework apps
 * (Silex, ..).
 */
class ParamConverterListener
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();
        $request = $event->getRequest();

        if (is_array($controller)) {
            $r = new \ReflectionMethod($controller[0], $controller[1]);
        } else {
            $r = new \ReflectionFunction($controller);
        }

        // automatically apply conversion for non-configured objects
        foreach ($r->getParameters() as $param) {
            if (!$param->getClass() || $param->getClass()->isInstance($request)) {
                continue;
            }

            $class = $param->getClass()->getName();
            $name = $param->getName();

            if ("QafooLabs\\MVC\\Flash" === $class) {
                $value = new SymfonyFlashBag($request->getSession()->getFlashBag());
            } else if (is_subclass_of($class, "Symfony\\Component\\HttpFoundation\\Session\\SessionInterface") ||
                   $class === "Symfony\\Component\\HttpFoundation\\Session\\SessionInterface") {
                $value = $request->getSession();
            } else if ("QafooLabs\\MVC\\FormRequest" === $class) {
                $value = new SymfonyFormRequest($request, $this->container->get('form.factory'));
            } else if ("QafooLabs\\MVC\\TokenContext" === $class) {
                $value = new SymfonyTokenContext(
                    $this->container->get('security.context')
                );
            } else {
                continue;
            }

            $request->attributes->set($name, $value);
        }
    }
}
