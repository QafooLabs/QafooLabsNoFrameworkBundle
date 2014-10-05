<?php
/**
 * QafooLabs NoFrameworkBundle
 *
 * LICENSE
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.txt.
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to kontakt@beberlei.de so I can send you a copy immediately.
 */

namespace QafooLabs\Bundle\NoFrameworkBundle\Tests\Templating;

use QafooLabs\Bundle\NoFrameworkBundle\Templating\JmsSerializerEngine;

class JmsSerializerEngineTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->serializer = \Phake::mock('JMS\Serializer\SerializerInterface');
        $this->engine = new JmsSerializerEngine($this->serializer);
    }

    /**
     * @test
     */
    public function it_renders_view_by_serialization()
    {
        $this->engine->render('Foo.xml.twig', array('view' => 'foo'));

        \Phake::verify($this->serializer)->serialize('foo', 'xml');
    }

    /**
     * @test
     */
    public function it_refuses_to_render_view_without_data()
    {
        $this->setExpectedException('RuntimeException', 'JmsSerializerEngine expects a template parameter "view" for serialization.');

        $this->engine->render('Foo.xml.twig', array());
    }

    /**
     * @test
     * @dataProvider supportedTemplateNames
     */
    public function it_supports_format_from_name($name)
    {
        $this->assertTrue($this->engine->supports($name));
    }

    static public function supportedTemplateNames()
    {
        $templateNames = array(
            'Foo.xml.twig',
            'Foo.json.twig',
            'Foo.xml.jms',
            'Foo.json.jms',
        );

        return array_map(function ($name) { return array($name); }, $templateNames);
    }
}
