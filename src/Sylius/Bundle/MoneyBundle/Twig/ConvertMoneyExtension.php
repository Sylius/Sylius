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

namespace Sylius\Bundle\MoneyBundle\Twig;

use Sylius\Bundle\MoneyBundle\Templating\Helper\ConvertMoneyHelperInterface;
use Sylius\Component\Currency\Converter\CurrencyConverterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class ConvertMoneyExtension extends AbstractExtension
{
    public function __construct(private ConvertMoneyHelperInterface|CurrencyConverterInterface $helper)
    {
        if ($this->helper instanceof ConvertMoneyHelperInterface) {
            trigger_deprecation(
                'sylius/money-bundle',
                '1.14',
                'Passing an instance of %s as constructor argument for %s is deprecated and will be prohibited in Sylius 2.0. Pass an instance of %s instead.',
                ConvertMoneyHelperInterface::class,
                self::class,
                CurrencyConverterInterface::class,
            );

            trigger_deprecation(
                'sylius/money-bundle',
                '1.14',
                'The argument name $helper is deprecated and will be renamed to $currencyConverter in Sylius 2.0.',
            );
        }
    }

    public function getFilters(): array
    {
        if ($this->helper instanceof CurrencyConverterInterface) {
            return [
                new TwigFilter('sylius_convert_money', [$this->helper, 'convert']),
            ];
        }

        return [
            new TwigFilter('sylius_convert_money', [$this->helper, 'convertAmount']),
        ];
    }
}
