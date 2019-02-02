<?php

namespace QafooLabs\Bundle\NoFrameworkBundle\Tests\Controller\ResultConverter;

use QafooLabs\Bundle\NoFrameworkBundle\Controller\ResultConverter\FlashYieldApplier;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use QafooLabs\MVC\Flash;

class FlashYieldApplierTest extends TestCase
{
    private $applier;

    public function setUp()
    {
        $this->applier = new FlashYieldApplier();
    }

    public function testSupportsOnlyFlash()
    {
        $this->assertTrue($this->applier->supports(new Flash('foo', 'bar')));
        $this->assertFalse($this->applier->supports(new \stdClass));
    }

    public function testApplySetsFlash()
    {
        $request = new Request();
        $session = new Session(new MockArraySessionStorage());
        $request->setSession($session);
        $response = new Response();

        $this->applier->apply(new Flash('foo', 'bar'), $request, $response);

        $this->assertEquals(['bar'], $request->getSession()->getFlashBag()->get('foo'));
    }
}
