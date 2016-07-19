<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Currency\Provider;

use Sylius\Component\Currency\Context\CurrencyNotFoundException;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Fernando Caraballo Ortiz <caraballo.ortiz@gmail.com>
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface CurrencyProviderInterface
{
    /**
     * @return string
     *
     * @throws CurrencyNotFoundException
     */
    public function getAvailableCurrenciesCodes();

    /**
     * @return string
     *
     * @throws CurrencyNotFoundException
     */
    public function getDefaultCurrencyCode();
}
