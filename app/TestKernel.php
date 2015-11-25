<?php

use Sylius\Bundle\CoreBundle\Kernel\Kernel;

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
     */
    protected function cleanupContainer($container)
    {
        $object = new \ReflectionObject($container);
        $property = $object->getProperty('services');
        $property->setAccessible(true);

        $services = $property->getValue($container) ?: [];
        foreach ($services as $id => $service) {
            if ('kernel' === $id) {
                continue;
            }

            $serviceObject = new \ReflectionObject($service);
            foreach ($serviceObject->getProperties() as $prop) {
                $prop->setAccessible(true);
                if ($prop->isStatic()) {
                    continue;
                }

                $prop->setValue($service, null);
            }
        }
        $property->setValue($container, null);
    }
}
