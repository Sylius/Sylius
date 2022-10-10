<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Util;

use Psr\EventDispatcher\EventDispatcherInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Event\RandomOrCreateResourceEvent;
use Sylius\Bundle\CoreBundle\DataFixtures\Event\ResourceEventInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ProductAssociationTypeFactoryInterface;
use Sylius\Component\Product\Model\ProductAssociationTypeInterface;
use Zenstruck\Foundry\Proxy;

trait RandomOrCreateProductAssociationTypeTrait
{
    /**
     * @return ProductAssociationTypeInterface|Proxy
     */
    private function randomOrCreateProductAssociationType(EventDispatcherInterface $eventDispatcher): Proxy
    {
        /** @var ResourceEventInterface $event */
        $event = $eventDispatcher->dispatch(
            new RandomOrCreateResourceEvent(ProductAssociationTypeFactoryInterface::class)
        );

        return $event->getResource();
    }
}
