<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\MoneyBundle\ExchangeRate\Provider;

/**
 * Interface ProviderInterface
 *
 * Exchange Rate Providers which implements ProviderInterface are responsible to get accurate exchange rate.
 *
 * @author Ivan Djurdjevac <djurdjevac@gmail.com>
 */
interface ProviderInterface
{
    /**
     * Get exchange rate value, usually from external service
     *
     * @param string $currencyFrom
     * @param string $currencyTo
     *
     * @return float
     */
    public function getRate($currencyFrom, $currencyTo);
}
