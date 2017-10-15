<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

require_once __DIR__.'/AppKernel.php';

use ProxyManager\Proxy\VirtualProxyInterface;
use PSS\SymfonyMockerContainer\DependencyInjection\MockerContainer;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
class TestAppKernel extends AppKernel
{
    /**
     * {@inheritdoc}
     */
    public function shutdown(): void
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
    protected function cleanupContainer(ContainerInterface $container): void
    {
        $containerReflection = new \ReflectionObject($container);
        $containerServicesPropertyReflection = $containerReflection->getProperty('services');
        $containerServicesPropertyReflection->setAccessible(true);

        $services = $containerServicesPropertyReflection->getValue($container) ?: [];
        foreach ($services as $serviceId => $service) {
            if (in_array($serviceId, $this->getServicesToIgnoreDuringContainerCleanup())) {
                continue;
            }

            $serviceReflection = new \ReflectionObject($service);

            if ($serviceReflection->implementsInterface(VirtualProxyInterface::class)) {
                continue;
            }

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

    protected function getContainerBaseClass(): string
    {
        return MockerContainer::class;
    }

    protected function getServicesToIgnoreDuringContainerCleanup(): array
    {
        return [
            'kernel',
            'http_kernel',
            'liip_imagine.mime_type_guesser',
            'liip_imagine.extension_guesser',
        ];
    }
}
