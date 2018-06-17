<?php

namespace QafooLabs\Bundle\NoFrameworkBundle\View;

use Symfony\Component\HttpKernel\KernelInterface;

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

        // Bundle::getParent was removed in Symfony 4
        if (!method_exists($bundle, 'getParent')) {
            return $bundle->getName();
        }

        while ($bundleName = $bundle->getName()) {
            if (null === $parentBundleName = $bundle->getParent()) {
                $bundleName = $bundle->getName();

                break;
            }

            $bundles = $this->kernel->getBundle($parentBundleName, false);
            $bundle = array_pop($bundles);
        }

        return $bundleName;
    }

    /**
     * Returns the Bundle instance in which the given class name is located.
     *
     * @param  string                    $class  A fully qualified controller class name
     * @param  Bundle                    $bundle A Bundle instance
     * @throws \InvalidArgumentException
     */
    protected function getBundleForClass($class)
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

        throw new \InvalidArgumentException(sprintf('The "%s" class does not belong to a registered bundle.', $class));
    }
}
