<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\MoneyBundle\Formatter;

use Webmozart\Assert\Assert;

final class MoneyFormatter implements MoneyFormatterInterface
{
    /**
     * {@inheritdoc}
     */
    public function format(int $amount, string $currency, ?string $locale = null): string
    {
        $formatter = new \NumberFormatter($locale ?? 'en', \NumberFormatter::CURRENCY);

        $result = $formatter->formatCurrency(abs($amount / 100), $currency);
        Assert::notSame(
            false,
            $result,
            sprintf('The amount "%s" of type %s cannot be formatted to currency "%s".', $amount, gettype($amount), $currency)
        );

        return $amount >= 0 ? $result : '-' . $result;
    }
}
