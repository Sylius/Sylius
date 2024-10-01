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

namespace Sylius\Bundle\CurrencyBundle\Templating\Helper;

use Symfony\Component\Intl\Currencies;
use Symfony\Component\Templating\Helper\Helper;

trigger_deprecation(
    'sylius/currency-bundle',
    '1.14',
    'The "%s" class is deprecated, use "%s::getSymbol" instead.',
    CurrencyHelper::class,
    Currencies::class,
);

/** @deprecated since Sylius 1.14 and will be removed in Sylius 2.0. Use {@see \Symfony\Component\Intl\Currencies} instead. */
class CurrencyHelper extends Helper implements CurrencyHelperInterface
{
    public function convertCurrencyCodeToSymbol(string $code): string
    {
        return Currencies::getSymbol($code);
    }

    public function getName(): string
    {
        return 'sylius_currency';
    }
}
