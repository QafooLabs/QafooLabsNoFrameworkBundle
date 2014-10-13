<?php

namespace QafooLabs\Bundle\NoFrameworkBundle\Tests\Request\ParamConverter;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use QafooLabs\Bundle\NoFrameworkBundle\Request\ParamConverter\FlashConverter;
use QafooLabs\MVC\Flash;

class FlashConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_converts_flash()
    {
        $converter = new FlashConverter();
        $session = new Session(new MockArraySessionStorage());
        $request = new Request();
        $request->setSession($session);

        $config = new ParamConverter(array('name' => 'foo'));

        $converter->apply($request, $config);

        $this->assertInstanceOf('QafooLabs\MVC\Flash', $request->attributes->get('foo'));
    }
}
