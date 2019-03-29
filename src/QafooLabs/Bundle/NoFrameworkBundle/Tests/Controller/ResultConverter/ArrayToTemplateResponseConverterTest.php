<?php

namespace QafooLabs\Bundle\NoFrameworkBundle\Tests\Controller\ResultConverter;

use PHPUnit\Framework\TestCase;

use QafooLabs\Bundle\NoFrameworkBundle\Controller\ResultConverter\ArrayToTemplateResponseConverter;
use Twig\Environment;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use QafooLabs\Bundle\NoFrameworkBundle\View\TemplateGuesser;
use QafooLabs\MVC\TemplateView;

class ArrayToTemplateResponseConverterTest extends TestCase
{
    private $twig;
    private $guesser;
    private $converter;

    public function setUp() : void
    {
        $this->converter = new ArrayToTemplateResponseConverter(
            $this->twig = \Phake::mock(Environment::class),
            $this->guesser = \Phake::mock(TemplateGuesser::class),
            'twig'
        );
    }

    public function testSupports()
    {
        $this->assertTrue($this->converter->supports(new TemplateView(['foo' => 'bar'])));
        $this->assertTrue($this->converter->supports([]));
    }

    public function testRenderArrayToTemplateStringFromController()
    {
        $request = new Request();
        $request->attributes->set('_controller', 'ctrl');

        \Phake::when($this->guesser)->guessControllerTemplateName('ctrl', null, 'html', 'twig')->thenReturn('ctrl.html.twig');

        $response = $this->converter->convert(['foo' => 'bar'], $request);

        \Phake::verify($this->twig)->render('ctrl.html.twig', ['foo' => 'bar', 'view' => ['foo' => 'bar']]);
    }
}
