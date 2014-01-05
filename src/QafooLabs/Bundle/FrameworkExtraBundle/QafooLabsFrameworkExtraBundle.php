<?php

namespace QafooLabs\Bundle\FrameworkExtraBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use QafooLabs\Bundle\FrameworkExtraBundle\DependencyInjection\Compiler\RoutingPass;

class QafooLabsFrameworkExtraBundle extends Bundle
{
    public function build(ContainerBuilder $builder)
    {
        $builder->addCompilerPass(new RoutingPass());
    }
}
