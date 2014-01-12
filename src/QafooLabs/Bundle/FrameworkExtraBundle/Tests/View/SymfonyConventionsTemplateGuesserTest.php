<?php

namespace QafooLabs\Bundle\FrameworkExtraBundle\Tests\View;

use QafooLabs\Bundle\FrameworkExtraBundle\View\SymfonyConventionsTemplateGuesser;

class SymfonyConventionsTemplateGuesserTest extends \PHPUnit_Framework_TestCase
{
    private $guesser;

    public function setUp()
    {
        $this->bundleLocation = \Phake::mock('QafooLabs\Bundle\FrameworkExtraBundle\View\BundleLocation');
        $this->parser = \Phake::mock('QafooLabs\Bundle\FrameworkExtraBundle\Controller\QafooControllerNameParser');

        $this->guesser = new SymfonyConventionsTemplateGuesser(
            $this->bundleLocation,
            $this->parser
        );
    }

    /**
     * @test
     */
    public function it_converts_controller_to_template_reference()
    {
        \Phake::when($this->bundleLocation)->locationFor('Controller\\FooController')->thenReturn('Bundle');

        $this->assertEquals(
            'Bundle:Foo:bar.html.twig',
            $this->guesser->guessControllerTemplateName('Controller\\FooController::barAction', 'html', 'twig')
        );
    }

    /**
     * @test
     */
    public function it_uses_parser_when_converting_non_callable_controller_to_template_reference()
    {
        \Phake::when($this->parser)->parse('foo:barAction')->thenReturn('Controller\\FooController::barAction');
        \Phake::when($this->bundleLocation)->locationFor('Controller\\FooController')->thenReturn('Bundle');

        $this->assertEquals(
            'Bundle:Foo:bar.html.twig',
            $this->guesser->guessControllerTemplateName('Controller\\FooController::barAction', 'html', 'twig')
        );
    }
}
