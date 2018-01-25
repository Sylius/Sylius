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

interface CurrencyNameConverterInterface
{
    /**
     * @param string $name
     * @param string|null $locale
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function convertToCode(string $name, ?string $locale = null): string;
}
