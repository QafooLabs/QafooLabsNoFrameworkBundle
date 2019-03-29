<?php

namespace QafooLabs\Bundle\NoFrameworkBundle\Tests\Controller;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;

use QafooLabs\Bundle\NoFrameworkBundle\Controller\ControllerUtils;
use Symfony\Component\Security\Csrf\CsrfToken;

class ControllerUtilsTest extends TestCase
{
    private $helper;
    private $container;

    public function setUp() : void
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
        $templating = $this->mockContainerService('twig', 'Twig\Environment');

        $this->helper->renderView('Foo', array('bar' => 'baz'));

        \Phake::verify($templating)->render('Foo', array('bar' => 'baz'));
    }

    /**
     * @test
     */
    public function it_renders_responses()
    {
        $templating = $this->mockContainerService('twig', 'Twig\Environment');

        $this->helper->render('Foo', array('bar' => 'baz'));

        \Phake::verify($templating)->render('Foo', array('bar' => 'baz'));
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
        $csrfProvider = $this->mockContainerService('security.csrf.token_manager', 'Symfony\Component\Security\Csrf\CsrfTokenManagerInterface');


        \Phake::when($csrfProvider)->isTokenValid()->thenReturn(false);

        $this->expectException('Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException');
        $this->helper->assertCsrfTokenValid('name', 'token');
    }

    /**
     * @test
     */
    public function it_generates_csrf_tokens()
    {
        $csrfProvider = $this->mockContainerService('security.csrf.token_manager', 'Symfony\Component\Security\Csrf\CsrfTokenManagerInterface');
        \Phake::when($csrfProvider)->getToken('name')->thenReturn(new CsrfToken('name', 'value'));

        $this->assertSame('value', $this->helper->generateCsrfToken('name'));
    }

    /**
     * @test
     */
    public function it_fails_returns_current_user_without_securitybundle()
    {
        $this->expectException('LogicException', 'The SecurityBundle');
        $this->helper->getUser();
    }

    /**
     * @test
     */
    public function it_returns_current_user()
    {
        $context = $this->mockContainerService('security.token_storage', 'Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface');
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
        $this->expectException('LogicException', 'The SecurityBundle');
        $this->helper->isGranted('foo');
    }

    /**
     * @test
     */
    public function it_grants_access()
    {
        $context = $this->mockContainerService('security.authorization_checker', 'Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface');

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
