<?php

namespace QafooLabs\Bundle\NoFrameworkBundle\Tests\Controller\ResultConverter;

use PHPUnit\Framework\TestCase;

use QafooLabs\Bundle\NoFrameworkBundle\Controller\ResultConverter\RedirectConverter;
use QafooLabs\MVC\RedirectRouteResponse;
use QafooLabs\MVC\RedirectRoute;

use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class RedirectConverterTest extends TestCase
{
    private $router;
    private $converter;

    public function setUp()
    {
        $this->router = \Phake::mock('Symfony\Component\Routing\Generator\UrlGeneratorInterface');
        $this->converter = new RedirectConverter($this->router);
    }

    /**
     * @test
     */
    public function it_redirects_when_goto_page_result()
    {
        \Phake::when($this->router)->generate('foo', array('id' => 10))->thenReturn('/foo?id=10');
        $request = Request::create('GET', '/');

        $response = $this->converter->convert(new RedirectRouteResponse('foo', array('id' => 10)), $request);

        $this->assertInstanceOf(
            'Symfony\Component\HttpFoundation\Response',
            $response
        );
        $this->assertTrue($response->isRedirect());

        $this->assertEquals('/foo?id=10', $response->headers->get('Location'));
    }

    /**
     * @test
     */
    public function it_redirects_when_redirect_route_result()
    {
        \Phake::when($this->router)->generate('foo', array('id' => 10))->thenReturn('/foo?id=10');
        $request = Request::create('GET', '/');

        $response = $this->converter->convert(new RedirectRoute('foo', array('id' => 10)), $request);

        $this->assertInstanceOf(
            'Symfony\Component\HttpFoundation\Response',
            $response
        );
        $this->assertTrue($response->isRedirect());

        $this->assertEquals('/foo?id=10', $response->headers->get('Location'));
    }
}

