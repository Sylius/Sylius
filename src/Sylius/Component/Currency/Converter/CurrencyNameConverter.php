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

namespace Sylius\Component\Currency\Converter;

use Symfony\Component\Intl\Currencies;

class CurrencyNameConverter implements CurrencyNameConverterInterface
{
    public function convertToCode(string $name, ?string $locale = null): string
    {
        $names = Currencies::getNames($locale ?? 'en');
        $currencyCode = array_search($name, $names, true);

        if (false === $currencyCode) {
            throw new \InvalidArgumentException(sprintf(
                'Currency "%s" not found! Available names: %s.',
                $name,
                implode(', ', $names),
            ));
        }

        return $currencyCode;
    }
}
