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

namespace Sylius\Bundle\MoneyBundle\Templating\Helper;

use Sylius\Component\Currency\Converter\CurrencyConverterInterface;

trigger_deprecation(
    'sylius/money-bundle',
    '1.14',
    'The "%s" interface is deprecated, use "%s" instead.',
    ConvertMoneyHelperInterface::class,
    CurrencyConverterInterface::class,
);

/** @deprecated since Sylius 1.14 and will be removed in Sylius 2.0. Use {@see \Sylius\Component\Currency\Converter\CurrencyConverterInterface} instead. */
interface ConvertMoneyHelperInterface
{
    /**
     * @throws \InvalidArgumentException
     */
    public function convertAmount(int $amount, ?string $sourceCurrencyCode, ?string $targetCurrencyCode): string;
}
