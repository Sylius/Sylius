<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Currency\Converter;

use Symfony\Component\Intl\Intl;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class CurrencyNameConverter implements CurrencyNameConverterInterface
{
    /**
     * {@inheritdoc}
     */
    public function convertToCode($name, $locale = 'en')
    {
        $names = Intl::getCurrencyBundle()->getCurrencyNames($locale);
        $currencyCode = array_search($name, $names, true);

        if (false === $currencyCode) {
            throw new \InvalidArgumentException(sprintf(
                'Currency "%s" not found! Available names: %s.', $name, implode(', ', $names)
            ));
        }

        return $currencyCode;
    }
}
