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

namespace Sylius\Component\Currency\Converter;

interface CurrencyConverterInterface
{
    public function convert(int $value, string $sourceCurrencyCode, string $targetCurrencyCode): int;
}
