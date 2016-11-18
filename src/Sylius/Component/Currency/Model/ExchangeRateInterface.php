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

use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
interface ExchangeRateInterface extends ResourceInterface
{
    /**
     * @return float
     */
    public function getRatio();

    /**
     * @param float $ratio
     */
    public function setRatio($ratio);

    /**
     * @return CurrencyInterface
     */
    public function getBaseCurrency();

    /**
     * @param CurrencyInterface $currency
     */
    public function setBaseCurrency(CurrencyInterface $currency);

    /**
     * @return CurrencyInterface
     */
    public function getCounterCurrency();

    /**
     * @param CurrencyInterface $currency
     */
    public function setCounterCurrency(CurrencyInterface $currency);
}
