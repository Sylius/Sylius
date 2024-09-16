<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Locale\Provider;

use Sylius\Component\Locale\Model\LocaleInterface;

final class LocaleProvider implements LocaleProviderInterface
{
    /** @param LocaleCollectionProviderInterface $localeRepository */
    public function __construct(
        private LocaleCollectionProviderInterface $localeRepository,
        private string $defaultLocaleCode,
    ) {
    }

    public function getAvailableLocalesCodes(): array
    {
        $locales = $this->localeRepository->getAll();

        return array_map(
            function (LocaleInterface $locale) {
                return (string) $locale->getCode();
            },
            $locales,
        );
    }

    public function getDefaultLocaleCode(): string
    {
        return $this->defaultLocaleCode;
    }
}
