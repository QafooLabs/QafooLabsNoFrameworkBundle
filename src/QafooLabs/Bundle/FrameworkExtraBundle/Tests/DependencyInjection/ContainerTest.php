<?php

namespace QafooLabs\Bundle\FrameworkExtraBundle\Tests\DependencyInjection;

use QafooLabs\Bundle\FrameworkExtraBundle\DependencyInjection\QafooLabsFrameworkExtraExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\Compiler\ResolveDefinitionTemplatesPass;

class ContainerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_compiles_with_container()
    {
        $container = $this->createContainer(array());

        $this->assertInstanceOf(
            'QafooLabs\Bundle\FrameworkExtraBundle\Controller\ControllerUtils',
            $container->get('qafoo_labs_framework_extra.controller_utils')
        );

        $this->assertInstanceOf(
            'QafooLabs\Bundle\FrameworkExtraBundle\EventListener\ViewListener',
            $container->get('qafoo_labs_framework_extra.view_listener')
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

        $this->assertEquals(array('foo' => 'bar'), $container->getParameter('qafoo_labs_framework_extra.convert_exceptions_map'));

        $this->assertInstanceOf(
            'QafooLabs\Bundle\FrameworkExtraBundle\EventListener\ConvertExceptionListener',
            $container->get('qafoo_labs_framework_extra.convert_exception_listener')
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

        $loader = new QafooLabsFrameworkExtraExtension();
        $container->set('templating', \Phake::mock('Symfony\Component\Templating\EngineInterface'));
        $container->set('kernel', \Phake::mock('Symfony\Component\HttpKernel\KernelInterface'));
        $container->set('controller_name_converter', \Phake::mock('Symfony\Bundle\FrameworkBundle\Controller\ControllerNameParser'));
        $container->set('logger', \Phake::mock('Psr\Log\LoggerInterface'));
        $container->registerExtension($loader);
        $loader->load(array($config), $container);

        $container->getCompilerPassConfig()->setOptimizationPasses(array(new ResolveDefinitionTemplatesPass()));
        $container->getCompilerPassConfig()->setRemovingPasses(array());
        $container->compile();

        return $container;
    }
}
