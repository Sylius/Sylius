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

use Sylius\Bundle\MoneyBundle\Formatter\MoneyFormatterInterface;

trigger_deprecation(
    'sylius/money-bundle',
    '1.14',
    'The "%s" interface is deprecated, use "%s" instead.',
    FormatMoneyHelperInterface::class,
    MoneyFormatterInterface::class,
);

/** @deprecated since Sylius 1.14 and will be removed in Sylius 2.0. Use {@see \Sylius\Bundle\MoneyBundle\Formatter\MoneyFormatterInterface} instead. */
interface FormatMoneyHelperInterface
{
    /**
     * @throws \InvalidArgumentException
     */
    public function formatAmount(int $amount, string $currencyCode, string $localeCode): string;
}
