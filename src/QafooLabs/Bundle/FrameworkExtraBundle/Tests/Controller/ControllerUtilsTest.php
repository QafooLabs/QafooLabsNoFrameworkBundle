<?php

namespace QafooLabs\Bundle\FrameworkExtraBundle\Tests\Controller;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;

use QafooLabs\Bundle\FrameworkExtraBundle\Controller\ControllerUtils;

class ControllerUtilsTest extends \PHPUnit_Framework_TestCase
{
    private $helper;
    private $container;

    public function setUp()
    {
        $this->container = new Container();
        $this->helper = new ControllerUtils($this->container);
    }

    /**
     * @test
     */
    public function it_generates_urls()
    {
        $router = $this->mockContainerService('router', 'Symfony\Component\Routing\Generator\UrlGenerator');

        $this->helper->generateUrl('foo', array('bar' => 'baz'), true);

        \Phake::verify($router)->generate('foo', array('bar' => 'baz'), true);
    }

    /**
     * @test
     */
    public function it_fowards()
    {
        $request = new Request();
        $this->container->set('request', $request);
        $kernel = $this->mockContainerService('http_kernel', 'Symfony\Component\HttpKernel\HttpKernelInterface');

        $this->helper->forward('ctrl');

        \Phake::verify($kernel)->handle(\Phake::anyParameters());
    }

    /**
     * @test
     */
    public function it_redirects()
    {
        $redirect = $this->helper->redirect('/url', 307);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $redirect);
        $this->assertEquals(307, $redirect->getStatusCode());
    }

    /**
     * @test
     */
    public function it_redirects_to_routes()
    {
        $router = $this->mockContainerService('router', 'Symfony\Component\Routing\Generator\UrlGenerator');
        \Phake::when($router)->generate('foo', array('bar' => 'baz'), true)->thenReturn('/url');

        $redirect = $this->helper->redirectRoute('foo', array('bar' => 'baz'), true, 307);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $redirect);
        $this->assertEquals(307, $redirect->getStatusCode());
    }

    /**
     * @test
     */
    public function it_renders_views()
    {
        $templating = $this->mockContainerService('templating', 'Symfony\Component\Templating\EngineInterface');

        $this->helper->renderView('Foo', array('bar' => 'baz'));

        \Phake::verify($templating)->render('Foo', array('bar' => 'baz'));
    }

    /**
     * @test
     */
    public function it_renders_responses()
    {
        $templating = $this->mockContainerService('templating', 'Symfony\Bundle\FrameworkBundle\Templating\EngineInterface');

        $this->helper->render('Foo', array('bar' => 'baz'));

        \Phake::verify($templating)->renderResponse('Foo', array('bar' => 'baz'), null);
    }

    /**
     * @test
     */
    public function it_streams_responses()
    {
        $templating = $this->mockContainerService('templating', 'Symfony\Bundle\FrameworkBundle\Templating\EngineInterface');

        $response = $this->helper->stream('Foo', array('bar' => 'baz'));

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\StreamedResponse', $response);
    }

    /**
     * @test
     */
    public function it_creates_not_found_exception()
    {
        $exception = $this->helper->createNotFoundException();

        $this->assertInstanceOf('Exception', $exception);
    }

    /**
     * @test
     */
    public function it_creates_access_denied_exception()
    {
        $exception = $this->helper->createAccessDeniedException();

        $this->assertInstanceOf('Exception', $exception);
    }

    /**
     * @test
     */
    public function it_creates_forms()
    {
        $formFactory = $this->mockContainerService('form.factory', 'Symfony\Component\Form\FormFactoryInterface');

        $form = $this->helper->createForm('type');

        \Phake::verify($formFactory)->create('type', null, array());
    }

    /**
     * @test
     */
    public function it_asserts_csrf_token_validity()
    {
        $csrfProvider = $this->mockContainerService('form.csrf_provider', 'Symfony\Component\Form\Extension\Csrf\CsrfProvider\CsrfProviderInterface');

        \Phake::when($csrfProvider)->isCsrfTokenValid('name', 'token')->thenReturn(false);

        $this->setExpectedException('Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException');
        $this->helper->assertCsrfTokenValid('name', 'token');
    }

    /**
     * @test
     */
    public function it_generates_csrf_tokens()
    {
        $csrfProvider = $this->mockContainerService('form.csrf_provider', 'Symfony\Component\Form\Extension\Csrf\CsrfProvider\CsrfProviderInterface');

        $this->helper->generateCsrfToken('name');

        \Phake::verify($csrfProvider)->generateCsrfToken('name');
    }

    /**
     * @test
     */
    public function it_fails_returns_current_user_without_securitybundle()
    {
        $this->setExpectedException('LogicException', 'The SecurityBundle');
        $user = $this->helper->getUser();
    }

    /**
     * @test
     */
    public function it_returns_current_user()
    {
        $context = $this->mockContainerService('security.context', 'Symfony\Component\Security\Core\SecurityContextInterface');
        $expectedUser = \Phake::mock('Symfony\Component\Security\Core\User\UserInterface');
        $token = new AnonymousToken('firewall', $expectedUser);
        \Phake::when($context)->getToken()->thenReturn($token);

        $user = $this->helper->getUser();

        $this->assertEquals($expectedUser, $user);
    }

    /**
     * @test
     */
    public function it_fails_granting_without_securitybundle()
    {
        $this->setExpectedException('LogicException', 'The SecurityBundle');
        $this->helper->isGranted('foo');
    }

    /**
     * @test
     */
    public function it_grants_access()
    {
        $context = $this->mockContainerService('security.context', 'Symfony\Component\Security\Core\SecurityContextInterface');

        $this->helper->isGranted('FOO');

        \Phake::verify($context)->isGranted('FOO', null);
    }

    /**
     * @test
     */
    public function it_translates()
    {
        $translator = $this->mockContainerService('translator', 'Symfony\Component\Translation\TranslatorInterface');

        $this->helper->translate('trans', array('foo' => 'bar'));

        \Phake::verify($translator)->trans('trans', array('foo' => 'bar'));
    }

    /**
     * @test
     */
    public function it_generates_json_response()
    {
        $response = $this->helper->json(array('foo' => 'bar'));

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\JsonResponse', $response);
    }

    private function mockContainerService($id, $class)
    {
        $service = \Phake::mock($class);
        $this->container->set($id, $service);

        return $service;
    }
}
