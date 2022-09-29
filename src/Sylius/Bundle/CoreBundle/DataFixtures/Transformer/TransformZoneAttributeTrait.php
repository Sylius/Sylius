<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Transformer;

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ZoneFactoryInterface;

trait TransformZoneAttributeTrait
{
    private ZoneFactoryInterface $zoneFactory;

    private function transformZoneAttribute(array $attributes): array
    {
        if (\is_string($attributes['zone'])) {
            $attributes['zone'] = $this->zoneFactory::findOrCreate(['code' => $attributes['zone']]);
        }

        return $attributes;
    }
}
