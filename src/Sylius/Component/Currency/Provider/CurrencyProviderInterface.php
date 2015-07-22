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

use Sylius\Component\Currency\Model\CurrencyInterface;

/**
 * This service returns all the available currencies.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface CurrencyProviderInterface
{
    /**
     * @return CurrencyInterface[]
     */
    public function getAvailableCurrencies();
}
