<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Transformer;

use Psr\EventDispatcher\EventDispatcherInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Event\FindOrCreateChannelByCodeEvent;

trait TransformChannelsAttributeTrait
{
    private EventDispatcherInterface $eventDispatcher;

    private function transformChannelsAttribute(array $attributes): array
    {
        $channels = [];
        foreach ($attributes['channels'] as $channel) {
            if (\is_string($channel)) {
                $event = new FindOrCreateChannelByCodeEvent($channel);
                $this->eventDispatcher->dispatch($event);

                if ($event->isPropagationStopped()) {
                    continue;
                }

                $channel = $event->getChannel();
            }
            $channels[] = $channel;
        }
        $attributes['channels'] = $channels;

        return $attributes;
    }
}
