<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Transformer;

use Psr\EventDispatcher\EventDispatcherInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Event\FindOrCreateChannelEvent;
use Sylius\Bundle\CoreBundle\DataFixtures\Event\FindOrCreateResourceEvent;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ChannelFactoryInterface;

trait TransformChannelAttributeTrait
{
    private EventDispatcherInterface $eventDispatcher;

    private function transformChannelAttribute(array $attributes): array
    {
        if (\is_string($attributes['channel'])) {
            /** @var FindOrCreateResourceEvent $event */
            $event = $this->eventDispatcher->dispatch(
                new FindOrCreateResourceEvent(ChannelFactoryInterface::class, ['code' => $attributes['channel']])
            );

            $attributes['channel'] = $event->getResource();
        }

        return $attributes;
    }
}
