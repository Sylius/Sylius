<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Util;

use Psr\EventDispatcher\EventDispatcherInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Event\FindOrCreateResourceEvent;
use Sylius\Bundle\CoreBundle\DataFixtures\Event\ResourceEventInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ChannelFactoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Zenstruck\Foundry\Proxy;

trait FindOrCreateChannelTrait
{
    /**
     * @return ChannelInterface|Proxy
     */
    private function findOrCreateChannel(EventDispatcherInterface $eventDispatcher, array $attributes): Proxy
    {
        /** @var ResourceEventInterface $event */
        $event = $eventDispatcher->dispatch(
            new FindOrCreateResourceEvent(ChannelFactoryInterface::class, $attributes),
        );

        return $event->getResource();
    }
}
