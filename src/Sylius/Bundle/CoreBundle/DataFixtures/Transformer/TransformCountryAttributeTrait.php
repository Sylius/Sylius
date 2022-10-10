<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Transformer;

use Psr\EventDispatcher\EventDispatcherInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Util\FindOrCreateCountryTrait;

trait TransformCountryAttributeTrait
{
    use FindOrCreateCountryTrait;

    private function transformCountryAttribute(EventDispatcherInterface $eventDispatcher, array $attributes): array
    {
        if (\is_string($attributes['country'])) {
            $attributes['country'] = $this->findOrCreateCountry($eventDispatcher, ['code' => $attributes['country']]);
        }

        return $attributes;
    }
}
