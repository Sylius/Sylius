<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Transformer;

use Psr\EventDispatcher\EventDispatcherInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Event\FindOrCreateChannelByCodeEvent;

trait TransformChannelAttributeTrait
{
    private EventDispatcherInterface $eventDispatcher;

    private function transformChannelAttribute(array $attributes): array
    {
        if (\is_string($attributes['channel'])) {
            $event = new FindOrCreateChannelByCodeEvent($attributes['channel']);
            $this->eventDispatcher->dispatch($event);

            if (!$event->isPropagationStopped() && null !== $event->getChannel()) {
                $attributes['channel'] = $event->getChannel();
            }
        }

        return $attributes;
    }
}
