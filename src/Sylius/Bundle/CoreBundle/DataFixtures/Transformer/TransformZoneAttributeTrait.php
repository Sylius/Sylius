<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Transformer;

use Psr\EventDispatcher\EventDispatcherInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Util\FindOrCreateZoneTrait;

trait TransformZoneAttributeTrait
{
    use FindOrCreateZoneTrait;

    private function transformZoneAttribute(EventDispatcherInterface $eventDispatcher, array $attributes): array
    {
        if (\is_string($attributes['zone'])) {
            $attributes['zone'] = $this->findOrCreateZone($eventDispatcher, ['code' => $attributes['zone']]);
        }

        return $attributes;
    }
}
