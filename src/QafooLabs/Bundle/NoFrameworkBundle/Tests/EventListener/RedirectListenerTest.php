<?php

namespace QafooLabs\Bundle\NoFrameworkBundle\Tests\EventListener;

use PHPUnit\Framework\TestCase;

use QafooLabs\Bundle\NoFrameworkBundle\EventListener\RedirectListener;
use QafooLabs\MVC\RedirectRouteResponse;
use QafooLabs\MVC\RedirectRoute;

use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class RedirectListenerTest extends TestCase
{
    private $router;
    private $listener;

    public function setUp()
    {
        $this->router = \Phake::mock('Symfony\Component\Routing\Generator\UrlGeneratorInterface');
        $this->listener = new RedirectListener($this->router);
    }

    /**
     * @test
     */
    public function it_redirects_when_goto_page_result()
    {
        \Phake::when($this->router)->generate('foo', array('id' => 10))->thenReturn('/foo?id=10');

        $event = $this->createEventWith(new RedirectRouteResponse('foo', array('id' => 10)));
        $this->listener->onKernelView($event);


        $this->assertInstanceOf(
            'Symfony\Component\HttpFoundation\Response',
            $event->getResponse()
        );
        $this->assertTrue($event->getResponse()->isRedirect());

        $this->assertEquals('/foo?id=10', $event->getResponse()->headers->get('Location'));
    }

    /**
     * @test
     */
    public function it_redirects_when_redirect_route_result()
    {
        \Phake::when($this->router)->generate('foo', array('id' => 10))->thenReturn('/foo?id=10');

        $event = $this->createEventWith(new RedirectRoute('foo', array('id' => 10)));
        $this->listener->onKernelView($event);


        $this->assertInstanceOf(
            'Symfony\Component\HttpFoundation\Response',
            $event->getResponse()
        );
        $this->assertTrue($event->getResponse()->isRedirect());

        $this->assertEquals('/foo?id=10', $event->getResponse()->headers->get('Location'));
    }

    private function createEventWith($controllerResult = null)
    {
        $request = Request::create('GET', '/');
        return new GetResponseForControllerResultEvent(
            \Phake::mock('Symfony\Component\HttpKernel\HttpKernelInterface'),
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            $controllerResult
        );
    }
}
