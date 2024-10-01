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

trigger_deprecation(
    'sylius/currency-bundle',
    '1.14',
    'The "%s" interface is deprecated.',
    CurrencyHelperInterface::class,
);

/** @deprecated since Sylius 1.14 and will be removed in Sylius 2.0. */
interface CurrencyHelperInterface
{
    public function convertCurrencyCodeToSymbol(string $code): string;
}
