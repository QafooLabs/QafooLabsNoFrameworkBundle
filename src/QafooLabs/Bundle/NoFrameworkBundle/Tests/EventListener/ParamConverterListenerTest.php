<?php

namespace QafooLabs\Bundle\NoFrameworkBundle\Tests\EventListener;

use QafooLabs\Bundle\NoFrameworkBundle\EventListener\ParamConverterListener;
use QafooLabs\MVC\FrameworkContext;
use QafooLabs\MVC\Flash;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class ParamConverterListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_converts_parameters()
    {
        $container = new Container;
        $container->set('security.context', $security = \Phake::mock('Symfony\Component\Security\Core\SecurityContextInterface'));

        $kernel = \Phake::mock('Symfony\Component\HttpKernel\HttpKernelInterface');
        $listener = new ParamConverterListener($container);

        $request = new Request();
        $request->setSession(new Session(new MockArraySessionStorage()));

        $event = new FilterControllerEvent($kernel, array($this, 'someAction'), $request, null);

        $listener->onKernelController($event);

        $this->assertTrue($request->attributes->has('flash'));
        $this->assertTrue($request->attributes->has('context'));
        $this->assertTrue($request->attributes->has('session'));
    }

    public function someAction(Session $session, FrameworkContext $context, Flash $flash)
    {
    }
}
