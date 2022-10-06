<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Transformer;

use Psr\EventDispatcher\EventDispatcherInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Event\FindOrCreateLocaleByQueryStringEvent;

trait TransformLocalesAttributeTrait
{
    private EventDispatcherInterface $eventDispatcher;

    private function transformLocalesAttribute(array $attributes): array
    {
        $locales = [];
        foreach ($attributes['locales'] as $locale) {
            if (\is_string($locale)) {
                $event = new FindOrCreateLocaleByQueryStringEvent($locale);
                $this->eventDispatcher->dispatch($event);

                $locale = $event->getLocale();
            }

            $locales[] = $locale;
        }
        $attributes['locales'] = $locales;

        return $attributes;
    }
}
