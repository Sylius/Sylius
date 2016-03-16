<?php

use PSS\SymfonyMockerContainer\DependencyInjection\MockerContainer;
use Sylius\Bundle\CoreBundle\Kernel\Kernel;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class TestKernel extends Kernel
{
    /**
     * {@inheritdoc}
     */
    public function shutdown()
    {
        if (false === $this->booted) {
            return;
        }

        if (!in_array($this->environment, ['test', 'test_cached'])) {
            parent::shutdown();

            return;
        }

        $container = $this->container;
        parent::shutdown();
        $this->cleanupContainer($container);
    }

    /**
     * Remove all container references from all loaded services
     *
     * @param ContainerInterface $container
     */
    protected function cleanupContainer(ContainerInterface $container)
    {
        $containerReflection = new \ReflectionObject($container);
        $containerServicesPropertyReflection = $containerReflection->getProperty('services');
        $containerServicesPropertyReflection->setAccessible(true);

        $services = $containerServicesPropertyReflection->getValue($container) ?: [];
        foreach ($services as $serviceId => $service) {
            if ('kernel' === $serviceId || 'http_kernel' === $serviceId) {
                continue;
            }

            $serviceReflection = new \ReflectionObject($service);

            $servicePropertiesReflections = $serviceReflection->getProperties();
            $servicePropertiesDefaultValues = $serviceReflection->getDefaultProperties();
            foreach ($servicePropertiesReflections as $servicePropertyReflection) {
                $defaultPropertyValue = null;
                if (isset($servicePropertiesDefaultValues[$servicePropertyReflection->getName()])) {
                    $defaultPropertyValue = $servicePropertiesDefaultValues[$servicePropertyReflection->getName()];
                }

                $servicePropertyReflection->setAccessible(true);
                $servicePropertyReflection->setValue($service, $defaultPropertyValue);
            }
        }

        $containerServicesPropertyReflection->setValue($container, null);
    }

    protected function getContainerBaseClass()
    {
        return MockerContainer::class;
    }
}
