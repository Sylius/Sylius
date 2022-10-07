<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Transformer;

use Psr\EventDispatcher\EventDispatcherInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Event\FindOrCreateResourceEvent;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\LocaleFactoryInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Util\FindOrCreateLocaleTrait;

trait TransformLocalesAttributeTrait
{
    use FindOrCreateLocaleTrait;

    private EventDispatcherInterface $eventDispatcher;

    private function transformLocalesAttribute(array $attributes): array
    {
        $locales = [];
        foreach ($attributes['locales'] as $locale) {
            if (\is_string($locale)) {
                $locale = $this->findOrCreateLocale(['code' => $locale]);
            }

            $locales[] = $locale;
        }
        $attributes['locales'] = $locales;

        return $attributes;
    }
}
