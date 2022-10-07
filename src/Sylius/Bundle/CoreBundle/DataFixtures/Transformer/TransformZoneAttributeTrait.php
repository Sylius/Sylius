<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Transformer;

use Psr\EventDispatcher\EventDispatcherInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Event\FindOrCreateResourceEvent;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ZoneFactoryInterface;

trait TransformZoneAttributeTrait
{
    private EventDispatcherInterface $eventDispatcher;

    private function transformZoneAttribute(array $attributes): array
    {
        if (\is_string($attributes['zone'])) {
            $event = new FindOrCreateResourceEvent(ZoneFactoryInterface::class, ['code' => $attributes['zone']]);
            $this->eventDispatcher->dispatch($event);

            $attributes['zone'] = $event->getResource();
        }

        return $attributes;
    }
}
