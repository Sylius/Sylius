<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\MoneyBundle\Formatter;

use Webmozart\Assert\Assert;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class MoneyFormatter implements MoneyFormatterInterface
{
    /**
     * {@inheritdoc}
     */
    public function format($amount, $currency, $locale = 'en')
    {
        $formatter = new \NumberFormatter($locale, \NumberFormatter::CURRENCY);

        $result = $formatter->formatCurrency(abs($amount / 100), $currency);
        Assert::notSame(
            false,
            $result,
            sprintf('The amount "%s" of type %s cannot be formatted to currency "%s".', $amount, gettype($amount), $currency)
        );

        return $amount >= 0 ? $result : '-' . $result;
    }
}
