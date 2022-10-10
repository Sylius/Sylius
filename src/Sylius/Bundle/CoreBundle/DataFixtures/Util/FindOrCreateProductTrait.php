<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Util;

use Psr\EventDispatcher\EventDispatcherInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Event\FindOrCreateResourceEvent;
use Sylius\Bundle\CoreBundle\DataFixtures\Event\ResourceEventInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ProductFactoryInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Zenstruck\Foundry\Proxy;

trait FindOrCreateProductTrait
{
    /**
     * @return ProductInterface|Proxy
     */
    private function findOrCreateProduct(EventDispatcherInterface $eventDispatcher, array $attributes): Proxy
    {
        /** @var ResourceEventInterface $event */
        $event = $eventDispatcher->dispatch(
            new FindOrCreateResourceEvent(ProductFactoryInterface::class, $attributes),
        );

        return $event->getResource();
    }
}
