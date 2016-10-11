<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Currency\Converter;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
interface CurrencyNameConverterInterface
{
    /**
     * @param string $name
     * @param string $locale
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function convertToCode($name, $locale);
}
