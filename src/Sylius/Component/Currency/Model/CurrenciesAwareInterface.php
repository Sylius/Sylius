<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
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
     * @return Collection<array-key, CurrencyInterface>
     */
    public function getCurrencies(): Collection;

    public function hasCurrency(CurrencyInterface $currency): bool;

    public function addCurrency(CurrencyInterface $currency): void;

    public function removeCurrency(CurrencyInterface $currency): void;
}
