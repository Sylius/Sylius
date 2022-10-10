<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Util;

use Psr\EventDispatcher\EventDispatcherInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Event\RandomOrCreateResourceEvent;
use Sylius\Bundle\CoreBundle\DataFixtures\Event\ResourceEventInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ZoneFactoryInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Zenstruck\Foundry\Proxy;

trait RandomOrCreateZoneTrait
{
    /**
     * @return ZoneInterface|Proxy
     */
    private function randomOrCreateZone(EventDispatcherInterface $eventDispatcher): Proxy
    {
        /** @var ResourceEventInterface $event */
        $event = $eventDispatcher->dispatch(
            new RandomOrCreateResourceEvent(ZoneFactoryInterface::class),
        );

        return $event->getResource();
    }
}
