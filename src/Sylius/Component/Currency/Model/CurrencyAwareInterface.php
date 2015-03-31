<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Currency\Model;

interface CurrencyAwareInterface
{
    /**
     * Get currency.
     *
     * @return string
     */
    public function getCurrency();

    /**
     * Set currency.
     *
     * @param string
     *
     * @return self
     */
    public function setCurrency($currency);
}
