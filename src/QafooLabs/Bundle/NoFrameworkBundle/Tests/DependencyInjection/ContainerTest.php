<?php

namespace QafooLabs\Bundle\NoFrameworkBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use QafooLabs\Bundle\NoFrameworkBundle\Controller\ControllerUtils;
use QafooLabs\Bundle\NoFrameworkBundle\DependencyInjection\QafooLabsNoFrameworkExtension;
use QafooLabs\Bundle\NoFrameworkBundle\EventListener\ViewListener;
use QafooLabs\Bundle\NoFrameworkBundle\Request\SymfonyFormRequest;
use QafooLabs\Bundle\NoFrameworkBundle\Request\SymfonyFormRequestFactory;
use QafooLabs\Bundle\NoFrameworkBundle\SymfonyTokenContext;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Bundle\FrameworkBundle\Controller\ControllerNameParser;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use QafooLabs\Bundle\NoFrameworkBundle\EventListener\ConvertExceptionListener;

class ContainerTest extends TestCase
{
    /**
     * @test
     * @dataProvider provideRegisteredServices
     */
    public function it_compiles_with_container(string $serviceId, string $expectedClass = null): void
    {
        $expectedClass = $expectedClass ?? $serviceId;
        $container = $this->createContainer([]);

        self::assertInstanceOf($expectedClass,$container->get($serviceId));
    }

    /**
     * @test
     */
    public function it_allows_configuring_convert_exceptions(): void
    {
        $container = $this->createContainer(array(
            'convert_exceptions' => array(
                'foo' => 'bar',
            )
        ));

        $this->assertEquals(array('foo' => 'bar'), $container->getParameter('qafoo_labs_noframework.convert_exceptions_map'));

        $this->assertInstanceOf(
            ConvertExceptionListener::class,
            $container->get('qafoo_labs_noframework.convert_exception_listener')
        );
    }

    public function provideRegisteredServices():iterable
    {
        yield ['qafoo_labs_noframework.controller_utils', ControllerUtils::class];
        yield ['qafoo_labs_noframework.view_listener', ViewListener::class];
        yield [SymfonyFormRequestFactory::class];
        yield [SymfonyFormRequest::class];
        yield [SymfonyTokenContext::class];
    }

    private function createContainer(array $config): ContainerBuilder
    {
        $container = new ContainerBuilder(new ParameterBag(array(
            'kernel.debug'       => false,
            'kernel.bundles'     => array(),
            'kernel.cache_dir'   => sys_get_temp_dir(),
            'kernel.environment' => 'test',
            'kernel.root_dir'    => __DIR__.'/../../../../' // src dir
        )));

        $requestStack = \Phake::mock(RequestStack::class);
        \Phake::when($requestStack)->getCurrentRequest()->thenReturn(new Request());

        $loader = new QafooLabsNoFrameworkExtension();
        $container->set('twig', \Phake::mock(Environment::class));
        $container->set('kernel', \Phake::mock(KernelInterface::class));
        $container->set('controller_name_converter', \Phake::mock(ControllerNameParser::class));
        $container->set('logger', \Phake::mock(LoggerInterface::class));
        $container->set('router', \Phake::mock(UrlGeneratorInterface::class));
        $container->set('request_stack', $requestStack);
        $container->set('form.factory', \Phake::mock(FormFactory::class));
        $container->set('security.token_storage', \Phake::mock(TokenStorage::class));
        $container->set('security.authorization_checker', \Phake::mock(AuthorizationChecker::class));
        $container->registerExtension($loader);
        $loader->load(array($config), $container);

        $container->getCompilerPassConfig()->setRemovingPasses(array());

        foreach ($container->getDefinitions() as $definition) {
            $definition->setPublic(true); // symfony 4 support
        }

        $container->compile();

        return $container;
    }
}
