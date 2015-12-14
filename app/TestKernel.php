<?php

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
            if ('kernel' === $serviceId) {
                continue;
            }

<<<<<<< HEAD
            $serviceReflection = new \ReflectionObject($service);

            $servicePropertiesReflections = $serviceReflection->getProperties();
            $servicePropertiesDefaultValues = $serviceReflection->getDefaultProperties();
            foreach ($servicePropertiesReflections as $servicePropertyReflection) {
                $defaultPropertyValue = null;
                if (isset($servicePropertiesDefaultValues[$servicePropertyReflection->getName()])) {
                    $defaultPropertyValue = $servicePropertiesDefaultValues[$servicePropertyReflection->getName()];
=======
            $serviceObject = new \ReflectionObject($service);

            $properties = $serviceObject->getProperties();
            // If it has a static property it could be a singleton, therefore we should not
            // reset it's properties
            foreach ($properties as $property) {
                if ($property->isStatic()) {
                    continue 2;
>>>>>>> Update TestKernel.php
                }
            }

<<<<<<< HEAD
                $servicePropertyReflection->setAccessible(true);
                $servicePropertyReflection->setValue($service, $defaultPropertyValue);
=======
            foreach ($properties as $property) {
                $property->setAccessible(true);
                $property->setValue($service, null);
>>>>>>> Update TestKernel.php
            }
        }

        $containerServicesPropertyReflection->setValue($container, null);
    }

    protected function getContainerBaseClass()
    {
        return '\PSS\SymfonyMockerContainer\DependencyInjection\MockerContainer';
    }
}