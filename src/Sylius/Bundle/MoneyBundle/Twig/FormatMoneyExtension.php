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

use Sylius\Bundle\MoneyBundle\Formatter\MoneyFormatterInterface;
use Sylius\Bundle\MoneyBundle\Templating\Helper\ConvertMoneyHelperInterface;
use Sylius\Bundle\MoneyBundle\Templating\Helper\FormatMoneyHelperInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class FormatMoneyExtension extends AbstractExtension
{
    public function __construct(private FormatMoneyHelperInterface|MoneyFormatterInterface $helper)
    {
        if ($this->helper instanceof ConvertMoneyHelperInterface) {
            trigger_deprecation(
                'sylius/money-bundle',
                '1.14',
                'Passing an instance of %s as constructor argument for %s is deprecated and will be prohibited in Sylius 2.0. Pass an instance of %s instead.',
                FormatMoneyHelperInterface::class,
                self::class,
                MoneyFormatterInterface::class,
            );

            trigger_deprecation(
                'sylius/money-bundle',
                '1.14',
                'The argument name $helper is deprecated and will be renamed to $moneyFormatter in Sylius 2.0.',
            );
        }
    }

    public function getFilters(): array
    {
        if ($this->helper instanceof MoneyFormatterInterface) {
            return [
                new TwigFilter('sylius_format_money', [$this->helper, 'format']),
            ];
        }

        return [
            new TwigFilter('sylius_format_money', [$this->helper, 'formatAmount']),
        ];
    }
}
