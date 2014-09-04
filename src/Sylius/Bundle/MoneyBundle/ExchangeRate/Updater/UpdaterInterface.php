<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\MoneyBundle\ExchangeRate\Updater;

/**
 * Interface UpdaterInterface
 *
 * UpdaterInterface responsibility is to update exchange rate value
 *
 * @author Ivan Djurdjevac <djurdjevac@gmail.com>
 */
interface UpdaterInterface
{
    /**
     * Update currency rate
     *
     * @param string $currency
     *
     * @return bool
     */
    public function updateRate($currency);

    /**
     * Update all currencies in system
     *
     * @return bool
     */
    public function updateAllRates();
}
