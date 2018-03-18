<?php

namespace QafooLabs\Bundle\NoFrameworkBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use QafooLabs\Bundle\NoFrameworkBundle\DependencyInjection\QafooLabsNoFrameworkExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class ContainerTest extends TestCase
{
    /**
     * @test
     */
    public function it_compiles_with_container()
    {
        $container = $this->createContainer(array());

        $this->assertInstanceOf(
            'QafooLabs\Bundle\NoFrameworkBundle\Controller\ControllerUtils',
            $container->get('qafoo_labs_noframework.controller_utils')
        );

        $this->assertInstanceOf(
            'QafooLabs\Bundle\NoFrameworkBundle\EventListener\ViewListener',
            $container->get('qafoo_labs_noframework.view_listener')
        );

        $this->assertInstanceOf(
            'QafooLabs\Bundle\NoFrameworkBundle\EventListener\ParamConverterListener',
            $container->get('qafoo_labs_noframework.param_converter_listener')
        );
    }

    /**
     * @test
     */
    public function it_allows_configuring_convert_exceptions()
    {
        $container = $this->createContainer(array(
            'convert_exceptions' => array(
                'foo' => 'bar',
            )
        ));

        $this->assertEquals(array('foo' => 'bar'), $container->getParameter('qafoo_labs_noframework.convert_exceptions_map'));

        $this->assertInstanceOf(
            'QafooLabs\Bundle\NoFrameworkBundle\EventListener\ConvertExceptionListener',
            $container->get('qafoo_labs_noframework.convert_exception_listener')
        );
    }

    public function createContainer(array $config)
    {
        $container = new ContainerBuilder(new ParameterBag(array(
            'kernel.debug'       => false,
            'kernel.bundles'     => array(),
            'kernel.cache_dir'   => sys_get_temp_dir(),
            'kernel.environment' => 'test',
            'kernel.root_dir'    => __DIR__.'/../../../../' // src dir
        )));

        $loader = new QafooLabsNoFrameworkExtension();
        $container->set('templating', \Phake::mock('Symfony\Component\Templating\EngineInterface'));
        $container->set('kernel', \Phake::mock('Symfony\Component\HttpKernel\KernelInterface'));
        $container->set('controller_name_converter', \Phake::mock('Symfony\Bundle\FrameworkBundle\Controller\ControllerNameParser'));
        $container->set('logger', \Phake::mock('Psr\Log\LoggerInterface'));
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
