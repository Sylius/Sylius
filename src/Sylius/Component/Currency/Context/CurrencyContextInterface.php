<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Currency\Context;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface CurrencyContextInterface
{
    // Key used to store the currency in storage.
    const STORAGE_KEY = '_sylius_currency';

    /**
     * @return string
     */
    public function getDefaultCurrency();

    /**
     * @return string
     */
    public function getCurrency();

    /**
     * @param string $currency
     */
    public function setCurrency($currency);
}
