<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Util;

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\LocaleFactoryInterface;

trait GetLocalesTrait
{
    private LocaleFactoryInterface $localeFactory;

    private function getLocales(): iterable
    {
        $locales = $this->localeFactory->all();

        if (0 === count($locales)) {
            $locales[] = $this->localeFactory::new()->withDefaultCode()->create();
        }

        foreach ($locales as $locale) {
            yield $locale->getCode();
        }
    }
}
