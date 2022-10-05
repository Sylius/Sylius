<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Transformer;

use Psr\EventDispatcher\EventDispatcherInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Event\FindOrCreateLocaleByCodeEvent;

trait TransformLocalesAttributeTrait
{
    private EventDispatcherInterface $eventDispatcher;

    private function transformLocalesAttribute(array $attributes): array
    {
        $locales = [];
        foreach ($attributes['locales'] as $locale) {
            if (\is_string($locale)) {
                $event = new FindOrCreateLocaleByCodeEvent($locale);
                $this->eventDispatcher->dispatch($event);

                if ($event->isPropagationStopped()) {
                    continue;
                }

                $locale = $event->getLocale();
            }

            $locales[] = $locale;
        }
        $attributes['locales'] = $locales;

        return $attributes;
    }
}
