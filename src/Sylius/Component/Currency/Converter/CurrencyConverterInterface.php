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
 * Interface to be implemented by the currency converter service.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface CurrencyConverterInterface
{
    /**
     * Convert the given value to equal amount with different target currency.
     *
     * @param int    $value
     * @param string $targetCurrencyCode
     *
     * @return int
     */
    public function convertFromBase($value, $targetCurrencyCode);

    /**
     * Convert the given value to equal amount from different source currency.
     *
     * @param int    $value
     * @param string $sourceCurrencyCode
     *
     * @return int
     */
    public function convertToBase($value, $sourceCurrencyCode);
}
