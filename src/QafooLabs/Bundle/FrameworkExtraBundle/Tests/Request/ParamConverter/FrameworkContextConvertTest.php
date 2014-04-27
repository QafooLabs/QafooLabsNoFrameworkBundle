<?php

namespace QafooLabs\Bundle\FrameworkExtraBundle\Tests\Request\ParamConverter;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use QafooLabs\Bundle\FrameworkExtraBundle\Request\ParamConverter\FrameworkContextConverter;

class FrameworkContextConvertTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_converts_framework_context()
    {
        $container = \Phake::mock('Symfony\Component\DependencyInjection\ContainerInterface');
        $converter = new FrameworkContextConverter($container);
        $request = new Request();
        $config = new ParamConverter(array('name' => 'foo'));

        \Phake::when($container)->get('security.context')->thenReturn(
            \Phake::mock('Symfony\Component\Security\Core\SecurityContextInterface')
        );

        $converter->apply($request, $config);

        $this->assertInstanceOf('QafooLabs\MVC\FrameworkContext', $request->attributes->get('foo'));
    }
}
