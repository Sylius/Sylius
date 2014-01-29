<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\MoneyBundle\Model;

use Sylius\Bundle\ResourceBundle\Model\TimestampableInterface;

interface ExchangeRateInterface extends TimestampableInterface
{
    /**
     * Get currency
     *
     * @return string
     */
    public function getCurrency();

    /**
     * Set currency
     *
     * @param string $currency
     */
    public function setCurrency($currency);

    /**
     * Get rate
     *
     * @return float
     */
    public function getRate();

    /**
     * Set rate
     *
     * @param float $rate
     */
    public function setRate($rate);
}
