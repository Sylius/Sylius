<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Currency\Context;

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
    public function getDefaultCurrencyCode();

    /**
     * @return string
     */
    public function getCurrencyCode();

    /**
     * @param string $currencyCode
     */
    public function setCurrencyCode($currencyCode);
}
