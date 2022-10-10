<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Util;

use Psr\EventDispatcher\EventDispatcherInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Event\FindOrCreateResourceEvent;
use Sylius\Bundle\CoreBundle\DataFixtures\Event\ResourceEventInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ProductAssociationTypeFactoryInterface;
use Sylius\Component\Product\Model\ProductAssociationTypeInterface;
use Zenstruck\Foundry\Proxy;

trait FindOrCreateProductAssociationTypeTrait
{
    /**
     * @return ProductAssociationTypeInterface|Proxy
     */
    private function findOrCreateProductAssociationType(EventDispatcherInterface $eventDispatcher, array $attributes): Proxy
    {
        /** @var ResourceEventInterface $event */
        $event = $eventDispatcher->dispatch(
            new FindOrCreateResourceEvent(ProductAssociationTypeFactoryInterface::class, $attributes),
        );

        return $event->getResource();
    }
}
