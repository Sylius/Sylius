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

namespace Sylius\Component\Currency\Converter;

interface CurrencyConverterInterface
{
    /**
     * @param int $value
     * @param string $sourceCurrencyCode
     * @param string $targetCurrencyCode
     *
     * @return int
     */
    public function convert(int $value, string $sourceCurrencyCode, string $targetCurrencyCode): int;
}
