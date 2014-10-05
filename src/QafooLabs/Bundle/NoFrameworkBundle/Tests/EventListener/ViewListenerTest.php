<?php

namespace QafooLabs\Bundle\NoFrameworkBundle\Tests\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

use QafooLabs\Bundle\NoFrameworkBundle\EventListener\ViewListener;
use QafooLabs\MVC\TemplateView;

class ViewListenerTest extends \PHPUnit_Framework_TestCase
{
    const A_CONTROLLER = 'foo';
    const A_TEMPLATE = 'bar';
    const A_TEMPLATE_OVERWRITE = 'baz';

    private $listener;
    private $guesser;
    private $templating;

    public function setUp()
    {
        $this->templating = \Phake::mock('Symfony\Component\Templating\EngineInterface');
        $this->guesser = \Phake::mock('QafooLabs\Bundle\NoFrameworkBundle\View\TemplateGuesser');
        $this->listener = new ViewListener($this->templating, $this->guesser, 'twig');
    }

    /**
     * @test
     */
    public function it_ignores_requests_without_controller()
    {
        $request = $this->requestForController(null);

        $this->listener->onKernelView($this->createEventWith($request));

        \Phake::verifyNoInteraction($this->templating);
        \Phake::verifyNoInteraction($this->guesser);
    }

    /**
     * @test
     */
    public function it_generates_response_with_controller_array_result()
    {
        $result = array('foo' => 'bar');

        $request = $this->requestForController(self::A_CONTROLLER);

        $this->expectGuesserToReturnATemplateForAController($request);

        $this->listener->onKernelView($this->createEventWith($request, $result));

        $expectedResult = array('foo' => 'bar', 'view' => array('foo' => 'bar'));

        \Phake::verify($this->templating)->render(self::A_TEMPLATE, $expectedResult);
    }

    /**
     * @test
     */
    public function it_generates_response_with_controller_object_result()
    {
        $result = new \stdClass;

        $request = $this->requestForController(self::A_CONTROLLER);

        $this->expectGuesserToReturnATemplateForAController($request);

        $this->listener->onKernelView($this->createEventWith($request, $result));

        $expectedResult = array('view' => $result);

        \Phake::verify($this->templating)->render(self::A_TEMPLATE, $expectedResult);
    }

    /**
     * @test
     */
    public function it_generates_response_for_template_view()
    {
        $result = new \stdClass;

        $request = $this->requestForController(self::A_CONTROLLER);

        $this->expectGuesserToReturnATemplateForAController($request, self::A_TEMPLATE_OVERWRITE);

        $templateView = new TemplateView($result, self::A_TEMPLATE_OVERWRITE);
        $this->listener->onKernelView($this->createEventWith($request, $templateView));

        $expectedResult = array('view' => $result);

        \Phake::verify($this->templating)->render(self::A_TEMPLATE, $expectedResult);
    }

    private function requestForController($controller)
    {
        $request = Request::create('GET', '/');
        $request->attributes->set('_controller', $controller);

        return $request;
    }

    private function expectGuesserToReturnATemplateForAController($request, $templateActionName = null)
    {
        \Phake::when($this->guesser)->guessControllerTemplateName(
            self::A_CONTROLLER,
            $templateActionName,
            $request->getRequestFormat(),
            'twig'
        )->thenReturn(self::A_TEMPLATE);
    }

    private function createEventWith(Request $request, $controllerResult = null)
    {
        return new GetResponseForControllerResultEvent(
            \Phake::mock('Symfony\Component\HttpKernel\HttpKernelInterface'),
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            $controllerResult
        );
    }
}
