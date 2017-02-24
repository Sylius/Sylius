<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once __DIR__.'/AppKernel.php';

use PSS\SymfonyMockerContainer\DependencyInjection\MockerContainer;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class TestAppKernel extends AppKernel
{
    /**
     * {@inheritdoc}
     */
    public function shutdown()
    {
        if (false === $this->booted) {
            return;
        }

        if (!in_array($this->getEnvironment(), ['test', 'test_cached'], true)) {
            parent::shutdown();

            return;
        }

        $container = $this->getContainer();
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

    /**
     * {@inheritdoc}
     */
    public function registerBundles()
    {
        return array_merge(parent::registerBundles(), [new DAMA\DoctrineTestBundle\DAMADoctrineTestBundle()]);
    }
}
