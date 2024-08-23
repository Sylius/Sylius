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

use Sylius\Component\Currency\Converter\CurrencyConverter;
use Sylius\Component\Currency\Converter\CurrencyConverterInterface;
use Symfony\Component\Templating\Helper\Helper;

trigger_deprecation(
    'sylius/money-bundle',
    '1.14',
    'The "%s" class is deprecated, use "%s" instead.',
    ConvertMoneyHelper::class,
    CurrencyConverter::class,
);

/** @deprecated since Sylius 1.14 and will be removed in Sylius 2.0. Use {@see \Sylius\Component\Currency\Converter\CurrencyConverter} instead. */
class ConvertMoneyHelper extends Helper implements ConvertMoneyHelperInterface
{
    public function __construct(private CurrencyConverterInterface $currencyConverter)
    {
    }

    public function convertAmount(int $amount, ?string $sourceCurrencyCode, ?string $targetCurrencyCode): string
    {
        return (string) $this->currencyConverter->convert($amount, $sourceCurrencyCode, $targetCurrencyCode);
    }

    public function getName(): string
    {
        return 'sylius_money_converter';
    }
}
