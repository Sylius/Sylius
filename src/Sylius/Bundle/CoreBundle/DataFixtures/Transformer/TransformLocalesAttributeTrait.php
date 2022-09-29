<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Transformer;

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\LocaleFactoryInterface;

trait TransformLocalesAttributeTrait
{
    private LocaleFactoryInterface $localeFactory;

    private function transformLocalesAttribute(array $attributes): array
    {
        $locales = [];
        foreach ($attributes['locales'] as $locale) {
            if (\is_string($locale)) {
                $locale = $this->localeFactory::findOrCreate(['code' => $locale]);
            }

            $locales[] = $locale;
        }
        $attributes['locales'] = $locales;

        return $attributes;
    }
}
