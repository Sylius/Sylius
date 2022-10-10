<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Util;

use Psr\EventDispatcher\EventDispatcherInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Event\FindOrCreateResourceEvent;
use Sylius\Bundle\CoreBundle\DataFixtures\Event\ResourceEventInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ProductAttributeFactoryInterface;
use Sylius\Component\Product\Model\ProductAttributeInterface;
use Zenstruck\Foundry\Proxy;

trait FindOrCreateProductAttributeTrait
{
    /**
     * @return ProductAttributeInterface|Proxy
     */
    private function findOrCreateProductAttribute(EventDispatcherInterface $eventDispatcher, array $attributes): Proxy
    {
        /** @var ResourceEventInterface $event */
        $event = $eventDispatcher->dispatch(
            new FindOrCreateResourceEvent(ProductAttributeFactoryInterface::class, $attributes),
        );

        return $event->getResource();
    }
}
