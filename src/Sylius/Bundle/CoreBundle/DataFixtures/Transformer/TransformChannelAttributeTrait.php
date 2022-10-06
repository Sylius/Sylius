<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Transformer;

use Psr\EventDispatcher\EventDispatcherInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Event\FindOrCreateChannelByQueryStringEvent;

trait TransformChannelAttributeTrait
{
    private EventDispatcherInterface $eventDispatcher;

    private function transformChannelAttribute(array $attributes): array
    {
        if (\is_string($attributes['channel'])) {
            $event = new FindOrCreateChannelByQueryStringEvent($attributes['channel']);
            $this->eventDispatcher->dispatch($event);

            $attributes['channel'] = $event->getChannel();
        }

        return $attributes;
    }
}
