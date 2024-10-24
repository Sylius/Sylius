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

namespace Sylius\Bundle\MoneyBundle\Formatter;

use Webmozart\Assert\Assert;

final class MoneyFormatter implements MoneyFormatterInterface
{
    public function format(int $amount, string $currencyCode, ?string $locale = null): string
    {
        $formatter = new \NumberFormatter($locale ?? 'en', \NumberFormatter::CURRENCY);

        $result = $formatter->formatCurrency(abs($amount / 100), $currencyCode);
        Assert::notSame(
            false,
            $result,
            sprintf('The amount "%s" of type %s cannot be formatted to currency "%s".', $amount, gettype($amount), $currencyCode),
        );

        return $amount >= 0 ? $result : '-' . $result;
    }
}
