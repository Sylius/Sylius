<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Util;

use Psr\EventDispatcher\EventDispatcherInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Event\FindOrCreateResourceEvent;
use Sylius\Bundle\CoreBundle\DataFixtures\Event\ResourceEventInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ZoneFactoryInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Zenstruck\Foundry\Proxy;

trait FindOrCreateZoneTrait
{
    /**
     * @return ZoneInterface|Proxy
     */
    private function findOrCreateZone(EventDispatcherInterface $eventDispatcher, array $attributes): Proxy
    {
        /** @var ResourceEventInterface $event */
        $event = $eventDispatcher->dispatch(
            new FindOrCreateResourceEvent(ZoneFactoryInterface::class, $attributes),
        );

        return $event->getResource();
    }
}
