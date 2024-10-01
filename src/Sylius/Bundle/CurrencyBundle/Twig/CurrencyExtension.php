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

namespace Sylius\Bundle\CurrencyBundle\Twig;

use Sylius\Bundle\CurrencyBundle\Templating\Helper\CurrencyHelperInterface;
use Symfony\Component\Intl\Currencies;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class CurrencyExtension extends AbstractExtension
{
    public function __construct(private ?CurrencyHelperInterface $helper = null)
    {
        if ($this->helper instanceof CurrencyHelperInterface) {
            trigger_deprecation(
                'sylius/currency-bundle',
                '1.14',
                'Passing an instance of %s as constructor argument for %s is deprecated and will be prohibited in Sylius 2.0.',
                CurrencyHelperInterface::class,
                self::class,
            );
        }
    }

    public function getFilters(): array
    {
        if ($this->helper === null) {
            return [
                new TwigFilter('sylius_currency_symbol', [$this, 'convertCurrencyCodeToSymbol']),
            ];
        }

        return [
            new TwigFilter('sylius_currency_symbol', [$this->helper, 'convertCurrencyCodeToSymbol']),
        ];
    }

    public function convertCurrencyCodeToSymbol(string $code): string
    {
        return Currencies::getSymbol($code);
    }
}
