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

use Doctrine\Common\Collections\Collection;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface CurrenciesAwareInterface
{
    /**
     * @return Collection|CurrencyInterface[]
     */
    public function getCurrencies();

    /**
     * @param CurrencyInterface $currency
     *
     * @return bool
     */
    public function hasCurrency(CurrencyInterface $currency);

    /**
     * @param CurrencyInterface $currency
     */
    public function addCurrency(CurrencyInterface $currency);

    /**
     * @param CurrencyInterface $currency
     */
    public function removeCurrency(CurrencyInterface $currency);
}
