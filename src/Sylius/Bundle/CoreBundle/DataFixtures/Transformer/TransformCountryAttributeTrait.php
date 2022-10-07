<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Transformer;

use Sylius\Bundle\CoreBundle\DataFixtures\Event\FindOrCreateResourceEvent;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\CountryFactoryInterface;

trait TransformCountryAttributeTrait
{
    private CountryFactoryInterface $countryFactory;

    private function transformCountryAttribute(array $attributes): array
    {
        if (\is_string($attributes['country'])) {
            $event = new FindOrCreateResourceEvent(CountryFactoryInterface::class, ['code' => $attributes['country']]);
            $this->eventDispatcher->dispatch($event);

            $attributes['country'] = $event->getResource();
        }

        return $attributes;
    }
}
