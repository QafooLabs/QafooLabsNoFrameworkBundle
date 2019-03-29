<?php

namespace QafooLabs\Bundle\NoFrameworkBundle\View;

use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpKernel\Bundle;

class BundleLocation
{
    private $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    public function locationFor($className)
    {
        $bundle = $this->getBundleForClass($className);

        if (!$bundle) {
            return;
        }

        return $bundle->getName();
    }

    protected function getBundleForClass(string $class) : ?Bundle
    {
        $reflectionClass = new \ReflectionClass($class);
        $bundles = $this->kernel->getBundles();

        do {
            $namespace = $reflectionClass->getNamespaceName();
            foreach ($bundles as $bundle) {
                if (0 === strpos($namespace, $bundle->getNamespace())) {
                    return $bundle;
                }
            }
            $reflectionClass = $reflectionClass->getParentClass();
        } while ($reflectionClass);

        return null;
    }
}
