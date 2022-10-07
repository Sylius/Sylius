<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Transformer;

use Psr\EventDispatcher\EventDispatcherInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Event\FindOrCreateResourceEvent;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ChannelFactoryInterface;

trait TransformChannelsAttributeTrait
{
    private EventDispatcherInterface $eventDispatcher;

    private function transformChannelsAttribute(array $attributes): array
    {
        $channels = [];
        foreach ($attributes['channels'] as $channel) {
            if (\is_string($channel)) {
                $event = new FindOrCreateResourceEvent(ChannelFactoryInterface::class, ['code' => $channel]);
                $this->eventDispatcher->dispatch($event);

                $channel = $event->getResource();
            }
            $channels[] = $channel;
        }
        $attributes['channels'] = $channels;

        return $attributes;
    }
}
