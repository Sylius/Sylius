<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Currency\Converter;

use Symfony\Component\Intl\Intl;

class CurrencyNameConverter implements CurrencyNameConverterInterface
{
    /**
     * {@inheritdoc}
     */
    public function convertToCode(string $name, ?string $locale = null): string
    {
        $names = Intl::getCurrencyBundle()->getCurrencyNames($locale ?? 'en');
        $currencyCode = array_search($name, $names, true);

        if (false === $currencyCode) {
            throw new \InvalidArgumentException(sprintf(
                'Currency "%s" not found! Available names: %s.', $name, implode(', ', $names)
            ));
        }

        return $currencyCode;
    }
}
