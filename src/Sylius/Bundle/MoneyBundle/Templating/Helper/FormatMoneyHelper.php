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

use Sylius\Bundle\MoneyBundle\Formatter\MoneyFormatter;
use Sylius\Bundle\MoneyBundle\Formatter\MoneyFormatterInterface;
use Symfony\Component\Templating\Helper\Helper;

trigger_deprecation(
    'sylius/money-bundle',
    '1.14',
    'The "%s" class is deprecated, use "%s" instead.',
    FormatMoneyHelper::class,
    MoneyFormatter::class,
);

/** @deprecated since Sylius 1.14 and will be removed in Sylius 2.0. Use {@see \Sylius\Bundle\MoneyBundle\Formatter\MoneyFormatter} instead. */
class FormatMoneyHelper extends Helper implements FormatMoneyHelperInterface
{
    public function __construct(private MoneyFormatterInterface $moneyFormatter)
    {
    }

    public function formatAmount(int $amount, string $currencyCode, string $localeCode): string
    {
        return $this->moneyFormatter->format($amount, $currencyCode, $localeCode);
    }

    public function getName(): string
    {
        return 'sylius_format_money';
    }
}
