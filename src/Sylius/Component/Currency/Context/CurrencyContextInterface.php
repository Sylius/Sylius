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

use Sylius\Component\Model\CurrencyInterface;

/**
 * Interface to be implemented by the service providing the currently used
 * currency.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface CurrencyContextInterface
{
    /**
     * Get the default currency.
     *
     * @return CurrencyInterface
     */
    public function getDefaultCurrency();

    /**
     * Get the currently active currency.
     *
     * @return CurrencyInterface
     */
    public function getCurrency();

    /**
     * Set the currently active currency.
     *
     * @param CurrencyInterface $currency
     */
    public function setCurrency($currency);
}
