<?php

namespace QafooLabs\Bundle\NoFrameworkBundle\Tests\EventListener;

use PHPUnit\Framework\TestCase;

use QafooLabs\Bundle\NoFrameworkBundle\EventListener\ParamConverterListener;
use QafooLabs\Bundle\NoFrameworkBundle\ParamConverter\SymfonyServiceProvider;
use QafooLabs\MVC\TokenContext;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class ParamConverterListenerTest extends TestCase
{
    /**
     * @test
     */
    public function it_converts_parameters()
    {
        $container = new Container;
        $container->set('security.token_storage', \Phake::mock('Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface'));
        $container->set('security.authorization_checker', \Phake::mock('Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface'));
        $serviceProvider = new SymfonyServiceProvider($container);

        $kernel = \Phake::mock('Symfony\Component\HttpKernel\HttpKernelInterface');
        $listener = new ParamConverterListener($serviceProvider);

        $request = new Request();
        $request->setSession(new Session(new MockArraySessionStorage()));

        $method = function(Session $session, TokenContext $context) {};
        $event = new FilterControllerEvent($kernel, $method, $request, HttpKernelInterface::MASTER_REQUEST);

        $listener->onKernelController($event);

        $this->assertTrue($request->attributes->has('context'));
        $this->assertTrue($request->attributes->has('session'));
    }
}
