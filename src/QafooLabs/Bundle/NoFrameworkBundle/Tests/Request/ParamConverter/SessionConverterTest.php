<?php

namespace QafooLabs\Bundle\NoFrameworkBundle\Tests\Request\ParamConverter;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use QafooLabs\Bundle\NoFrameworkBundle\Request\ParamConverter\SessionConverter;

class SessionConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_converts_session()
    {
        $converter = new SessionConverter();
        $session = new Session();
        $request = new Request();
        $request->setSession($session);

        $config = new ParamConverter(array('name' => 'foo'));

        $converter->apply($request, $config);

        $this->assertSame($session, $request->attributes->get('foo'));
    }
}
