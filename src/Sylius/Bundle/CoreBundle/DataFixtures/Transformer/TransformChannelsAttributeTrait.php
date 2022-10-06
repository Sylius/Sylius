<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Transformer;

use Psr\EventDispatcher\EventDispatcherInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Event\FindOrCreateChannelByQueryStringEvent;

trait TransformChannelsAttributeTrait
{
    private EventDispatcherInterface $eventDispatcher;

    private function transformChannelsAttribute(array $attributes): array
    {
        $channels = [];
        foreach ($attributes['channels'] as $channel) {
            if (\is_string($channel)) {
                $event = new FindOrCreateChannelByQueryStringEvent($channel);
                $this->eventDispatcher->dispatch($event);

                $channel = $event->getChannel();
            }
            $channels[] = $channel;
        }
        $attributes['channels'] = $channels;

        return $attributes;
    }
}
