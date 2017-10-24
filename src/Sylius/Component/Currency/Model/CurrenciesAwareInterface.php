<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Currency\Model;

use Doctrine\Common\Collections\Collection;

interface CurrenciesAwareInterface
{
    /**
     * @return Collection|CurrencyInterface[]
     */
    public function getCurrencies(): Collection;

    /**
     * @param CurrencyInterface $currency
     *
     * @return bool
     */
    public function hasCurrency(CurrencyInterface $currency): bool;

    /**
     * @param CurrencyInterface $currency
     */
    public function addCurrency(CurrencyInterface $currency): void;

    /**
     * @param CurrencyInterface $currency
     */
    public function removeCurrency(CurrencyInterface $currency): void;
}
