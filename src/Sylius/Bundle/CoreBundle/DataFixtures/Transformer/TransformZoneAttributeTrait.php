<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Transformer;


use Psr\EventDispatcher\EventDispatcherInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Event\FindOrCreateZoneByQueryStringEvent;

trait TransformZoneAttributeTrait
{
    private EventDispatcherInterface $eventDispatcher;

    private function transformZoneAttribute(array $attributes): array
    {
        if (\is_string($attributes['zone'])) {
            $event = new FindOrCreateZoneByQueryStringEvent($attributes['zone']);
            $this->eventDispatcher->dispatch($event);

            $attributes['zone'] = $event->getZone();
        }

        return $attributes;
    }
}
