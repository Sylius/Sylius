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
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface CurrencyConverterInterface
{
    /**
     * @param int $value
     * @param string $targetCurrencyCode
     *
     * @return int
     *
     * @throws UnavailableCurrencyException
     */
    public function convertFromBase($value, $targetCurrencyCode);

    /**
     * @param int $value
     * @param string $sourceCurrencyCode
     *
     * @return int
     *
     * @throws UnavailableCurrencyException
     */
    public function convertToBase($value, $sourceCurrencyCode);
}
