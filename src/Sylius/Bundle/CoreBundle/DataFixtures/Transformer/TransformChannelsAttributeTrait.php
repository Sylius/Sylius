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
                /** @var FindOrCreateResourceEvent $event */
                $event = $this->eventDispatcher->dispatch(
                    new FindOrCreateResourceEvent(ChannelFactoryInterface::class, ['code' => $channel])
                );

                $channel = $event->getResource();
            }
            $channels[] = $channel;
        }
        $attributes['channels'] = $channels;

        return $attributes;
    }
}
