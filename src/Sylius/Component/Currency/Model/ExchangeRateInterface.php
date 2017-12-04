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

use Sylius\Component\Resource\Model\ResourceInterface;

interface ExchangeRateInterface extends ResourceInterface
{
    /**
     * @return float|null
     */
    public function getRatio(): ?float;

    /**
     * @param float|null $ratio
     */
    public function setRatio(?float $ratio);

    /**
     * @return CurrencyInterface|null
     */
    public function getSourceCurrency(): ?CurrencyInterface;

    /**
     * @param CurrencyInterface $currency
     */
    public function setSourceCurrency(CurrencyInterface $currency): void;

    /**
     * @return CurrencyInterface|null
     */
    public function getTargetCurrency(): ?CurrencyInterface;

    /**
     * @param CurrencyInterface $currency
     */
    public function setTargetCurrency(CurrencyInterface $currency): void;
}
