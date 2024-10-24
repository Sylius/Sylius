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

namespace Sylius\Component\Locale\Context;

use Sylius\Component\Locale\Provider\LocaleProviderInterface;

final class ProviderBasedLocaleContext implements LocaleContextInterface
{
    public function __construct(private LocaleProviderInterface $localeProvider)
    {
    }

    public function getLocaleCode(): string
    {
        $availableLocalesCodes = $this->localeProvider->getAvailableLocalesCodes();
        $localeCode = $this->localeProvider->getDefaultLocaleCode();

        if (!in_array($localeCode, $availableLocalesCodes, true)) {
            throw LocaleNotFoundException::notAvailable($localeCode, $availableLocalesCodes);
        }

        return $localeCode;
    }
}
